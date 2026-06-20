<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IssueCollection;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CitizenPortalController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $issues = Issue::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Citizen dashboard data fetched successfully.',
            'data' => [
                'profile' => $this->formatProfile($user),
                'stats' => [
                    'total_reported' => $issues->count(),
                    'in_progress' => $issues->where('status', 'in_progress')->count(),
                    'resolved' => $issues->where('status', 'resolved')->count(),
                    'upvotes_received' => (int) $issues->sum('upvotes_count'),
                ],
                'recent_issues' => $issues->take(5)->map(fn (Issue $issue): array => $this->formatIssueSummary($issue))->values(),
            ],
        ]);
    }

    public function issues(Request $request): IssueCollection
    {
        /** @var User $user */
        $user = $request->user();

        $issues = Issue::query()
            ->where('user_id', $user->id)
            ->with(['user', 'images', 'worker.department'])
            ->withCount('comments')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = $request->string('search')->toString();

                $query->where(function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('address', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(max(1, min((int) $request->integer('limit', 10), 20)));

        return new IssueCollection($issues);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'preferred_location' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => [
                'profile' => $this->formatProfile($user->fresh()),
            ],
        ]);
    }

    public function settings(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'message' => 'Citizen settings fetched successfully.',
            'data' => [
                'settings' => [
                    'theme_preference' => $user->theme_preference ?? 'light',
                ],
            ],
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'theme_preference' => ['required', Rule::in(['light', 'dark'])],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Settings updated successfully.',
            'data' => [
                'settings' => [
                    'theme_preference' => $user->theme_preference,
                ],
            ],
        ]);
    }

    private function formatProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'preferred_location' => $user->preferred_location,
            'theme_preference' => $user->theme_preference ?? 'light',
            'text_size' => $user->text_size ?? 'default',
        ];
    }

    private function formatIssueSummary(Issue $issue): array
    {
        return [
            'id' => $issue->id,
            'title' => $issue->title,
            'status' => $issue->status,
            'category' => $issue->category,
            'address' => $issue->address,
            'reported_at' => optional($issue->reported_at)->toISOString(),
            'deadline' => optional($issue->deadline)->toDateString(),
            'upvotes_count' => $issue->upvotes_count,
        ];
    }
}
