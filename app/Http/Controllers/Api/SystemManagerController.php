<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class SystemManagerController extends Controller
{
    public function municipalManager(): JsonResponse
    {
        $manager = User::query()
            ->where('role', User::ROLE_MUNICIPAL_MANAGER)
            ->first();

        return response()->json([
            'message' => 'Municipal manager fetched successfully.',
            'data' => [
                'municipal_manager' => $manager,
            ],
        ]);
    }

    public function storeMunicipalManager(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
                'phone' => ['nullable', 'string', 'max:25'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            if (User::query()->where('role', User::ROLE_MUNICIPAL_MANAGER)->exists()) {
                return response()->json([
                    'message' => 'A municipal manager already exists.',
                ], 422);
            }

            $manager = User::create([
                ...$validated,
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_MUNICIPAL_MANAGER,
            ]);

            return response()->json([
                'message' => 'Municipal manager created successfully.',
                'data' => [
                    'municipal_manager' => $manager,
                ],
            ], 201);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to create municipal manager.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function updateMunicipalManager(Request $request, User $user): JsonResponse
    {
        try {
            if ($user->role !== User::ROLE_MUNICIPAL_MANAGER) {
                return response()->json([
                    'message' => 'The selected user is not the municipal manager.',
                ], 422);
            }

            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
                'phone' => ['sometimes', 'nullable', 'string', 'max:25'],
                'password' => ['sometimes', 'string', 'min:8'],
            ]);

            if (array_key_exists('password', $validated)) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            return response()->json([
                'message' => 'Municipal manager updated successfully.',
                'data' => [
                    'municipal_manager' => $user->fresh(),
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to update municipal manager.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function escalations(Request $request): JsonResponse
    {
        try {
            $issues = Issue::query()
                ->with(['user', 'assignment.worker', 'assignment.assignedBy', 'escalatedTo'])
                ->where('status', 'escalated')
                ->latest('escalated_at')
                ->paginate(max(1, min((int) $request->integer('limit', 10), 20)));

            return response()->json([
                'message' => 'Escalated complaints fetched successfully.',
                'data' => $issues,
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to fetch escalations.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function analytics(): JsonResponse
    {
        try {
            $summary = [
                'total_complaints' => Issue::count(),
                'reported_count' => Issue::where('status', 'reported')->count(),
                'assigned_count' => Issue::where('status', 'assigned')->count(),
                'in_progress_count' => Issue::where('status', 'in_progress')->count(),
                'resolved_count' => Issue::where('status', 'resolved')->count(),
                'escalated_count' => Issue::where('status', 'escalated')->count(),
                'urgent_count' => Issue::where('priority', 'urgent')->count(),
            ];

            return response()->json([
                'message' => 'System analytics fetched successfully.',
                'data' => [
                    'analytics' => $summary,
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to fetch analytics.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
