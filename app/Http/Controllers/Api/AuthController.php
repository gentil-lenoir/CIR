<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email'), Rule::unique('workers', 'email')],
            'phone' => ['required', 'digits:10'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            ...$validated,
            'role' => 'citizen',
        ]);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $worker = Worker::query()
            ->with('department')
            ->where('email', $validated['email'])
            ->first();

        if ($worker && Hash::check($validated['password'], $worker->password)) {
            if ($worker->status !== Worker::STATUS_ACTIVE) {
                return response()->json([
                    'message' => 'This worker account is inactive. Please contact the municipal manager.',
                ], 403);
            }

            $token = $worker->createToken('worker-api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful.',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $this->formatAuthenticatedPrincipal($worker),
                'data' => [
                    'user' => $this->formatAuthenticatedPrincipal($worker),
                    'token' => $token,
                ],
            ]);
        }

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 422);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatAuthenticatedPrincipal($user),
            'data' => [
                'user' => $this->formatAuthenticatedPrincipal($user),
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $principal = $request->user();

        if ($principal instanceof Worker) {
            $principal->loadMissing('department');
        }

        return response()->json([
            'message' => 'Authenticated user fetched successfully.',
            'user' => $this->formatAuthenticatedPrincipal($principal),
            'data' => [
                'user' => $this->formatAuthenticatedPrincipal($principal),
            ],
        ]);
    }

    private function formatAuthenticatedPrincipal(?Authenticatable $principal): array|null
    {
        if (! $principal) {
            return null;
        }

        if ($principal instanceof User && $principal->role === User::ROLE_WORKER) {
            $principal = $this->provisionWorkerProfileFromLegacyUser($principal);
        }

        if ($principal instanceof Worker) {
            return [
                'id' => $principal->id,
                'name' => $principal->name,
                'email' => $principal->email,
                'phone' => $principal->phone,
                'role' => Worker::ROLE,
                'status' => $principal->status,
                'availability_status' => $principal->availability_status,
                'theme_preference' => $principal->theme_preference ?? 'light',
                'department' => $principal->department ? [
                    'id' => $principal->department->id,
                    'name' => $principal->department->name,
                ] : null,
            ];
        }

        return [
            'id' => $principal->id,
            'name' => $principal->name,
            'email' => $principal->email,
            'phone' => $principal->phone,
            'role' => $principal->role,
            'preferred_location' => $principal->preferred_location,
            'theme_preference' => $principal->theme_preference ?? 'light',
        ];
    }

    private function provisionWorkerProfileFromLegacyUser(User $user): Worker
    {
        $worker = Worker::query()->where('email', $user->email)->first();

        if ($worker) {
            return $worker->load('department');
        }

        $departmentId = $user->getAttribute('department_id')
            ?? Department::query()->value('id');

        return tap(new Worker(['email' => $user->email]), function (Worker $worker) use ($user, $departmentId): void {
            $worker->fill([
                'name' => $user->name,
                'phone' => $user->phone,
                'department_id' => $departmentId,
                'password' => $user->getAuthPassword(),
                'status' => Worker::STATUS_ACTIVE,
                'availability_status' => $user->availability_status ?? 'available',
                'theme_preference' => $user->theme_preference ?? 'light',
                'preferred_zone' => $user->preferred_zone,
                'shift_window' => $user->shift_window,
            ]);
            $worker->save();
        })->load('department');
    }
}
