<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IssueCollection;
use App\Http\Resources\IssueResource;
use App\Models\Issue;
use App\Models\IssueImage;
use App\Models\IssueStatusHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class IssueController extends Controller
{
    public function index(Request $request): IssueCollection|JsonResponse
    {
        try {
            $perPage = max(1, min((int) $request->integer('limit', 10), 20));
            $issues = Issue::query()
                ->with(['user', 'images', 'assignment.worker', 'assignment.assignedBy', 'escalatedTo', 'worker.department'])
                ->withCount('comments')
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->when($request->boolean('escalated_only'), fn ($query) => $query->where('status', 'escalated'))
                ->latest()
                ->paginate($perPage);

            return new IssueCollection($issues);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to fetch issues.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $totalIssues = Issue::count();
            $resolvedIssues = Issue::where('status', 'resolved')->count();
            $pendingIssues = Issue::whereNotIn('status', ['resolved', 'rejected'])->count();

            return response()->json([
                'message' => 'System stats fetched successfully.',
                'data' => [
                    'total_issues' => $totalIssues,
                    'resolved_issues' => $resolvedIssues,
                    'pending_issues' => $pendingIssues,
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to fetch system stats.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): IssueResource|JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'category' => ['nullable', 'string', 'max:100'],
                'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'address' => ['nullable', 'string', 'max:255'],
                'images' => ['nullable', 'array', 'max:5'],
                'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            ]);

            $issue = DB::transaction(function () use ($request, $validated) {
                $issue = Issue::create([
                    ...$validated,
                    'user_id' => $request->user()->id,
                    'status' => 'reported',
                ]);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $path = $image->store('issues', 'public');

                        IssueImage::create([
                            'issue_id' => $issue->id,
                            'image_path' => $path,
                            'image_url' => Storage::disk('public')->url($path),
                        ]);
                    }
                }

                IssueStatusHistory::create([
                    'issue_id' => $issue->id,
                    'changed_by' => $request->user()->id,
                    'from_status' => null,
                    'to_status' => 'reported',
                    'notes' => 'Issue reported by citizen.',
                ]);

                return $issue;
            });

            $issue->load(['user', 'images', 'assignment.worker', 'assignment.assignedBy', 'escalatedTo']);
            $issue->load('worker.department');
            $issue->loadCount('comments');

            return new IssueResource($issue);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to create issue.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, int $id): IssueResource|JsonResponse
    {
        try {
            $issue = Issue::with([
                'user',
                'images',
                'comments.user',
                'upvotes',
                'assignment.worker',
                'assignment.assignedBy',
                'escalatedTo',
                'statusHistory.changedBy',
                'worker.department',
            ])->withCount('comments')->findOrFail($id);

            if ($request->user() && ! $this->canAccessIssue($request->user(), $issue)) {
                return response()->json([
                    'message' => 'You do not have permission to view this issue.',
                ], 403);
            }

            return new IssueResource($issue);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Issue not found.',
                'error' => $exception->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, int $id): IssueResource|JsonResponse
    {
        try {
            $issue = Issue::with('assignment')->findOrFail($id);
            $user = $request->user();

            if (! $this->canAccessIssue($user, $issue)) {
                return response()->json([
                    'message' => 'You do not have permission to update this issue.',
                ], 403);
            }

            if ($user->role === User::ROLE_WORKER) {
                return response()->json([
                    'message' => 'Workers must use the worker status endpoint for complaint progress updates.',
                ], 422);
            }

            $validated = $request->validate($this->rulesForUpdateRole($user));

            DB::transaction(function () use ($issue, $validated, $user) {
                $oldStatus = $issue->status;
                $updates = collect($validated)
                    ->except(['status_note', 'feedback'])
                    ->all();

                if (array_key_exists('feedback', $validated)) {
                    $updates['citizen_feedback'] = $validated['feedback'];
                    $updates['feedback_submitted_at'] = now();
                }

                if (array_key_exists('status', $validated) && $validated['status'] === 'escalated') {
                    $updates['escalated_at'] = now();
                    $updates['escalated_to'] = User::query()
                        ->where('role', User::ROLE_SYSTEM_MANAGER)
                        ->value('id');
                }

                if (array_key_exists('status', $validated) && $validated['status'] !== 'escalated') {
                    $updates['escalated_at'] = null;
                    $updates['escalated_to'] = null;
                }

                $issue->fill($updates);
                $issue->save();

                if (array_key_exists('status', $validated) && $validated['status'] !== $oldStatus) {
                    IssueStatusHistory::create([
                        'issue_id' => $issue->id,
                        'changed_by' => $user->id,
                        'from_status' => $oldStatus,
                        'to_status' => $validated['status'],
                        'notes' => $validated['status_note'] ?? null,
                    ]);

                }
            });

            $issue->load(['user', 'images', 'assignment.worker', 'assignment.assignedBy', 'escalatedTo']);
            $issue->load('worker.department');
            $issue->loadCount('comments');

            return new IssueResource($issue);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to update issue.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $issue = Issue::with('images')->findOrFail($id);

            if (
                ! in_array($request->user()->role, [User::ROLE_SYSTEM_MANAGER, User::ROLE_MUNICIPAL_MANAGER], true)
                && $issue->user_id !== $request->user()->id
            ) {
                return response()->json([
                    'message' => 'You do not have permission to delete this issue.',
                ], 403);
            }

            DB::transaction(function () use ($issue) {
                foreach ($issue->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                }

                $issue->delete();
            });

            return response()->json([
                'message' => 'Issue deleted successfully.',
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to delete issue.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    private function canAccessIssue(User $user, Issue $issue): bool
    {
        if (in_array($user->role, [User::ROLE_SYSTEM_MANAGER, User::ROLE_MUNICIPAL_MANAGER], true)) {
            return true;
        }

        if ($user->role === User::ROLE_WORKER) {
            return $issue->assignment?->worker_id === $user->id;
        }

        return $issue->user_id === $user->id;
    }

    private function rulesForUpdateRole(User $user): array
    {
        if ($user->role === User::ROLE_CITIZEN) {
            return [
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'string'],
                'category' => ['sometimes', 'nullable', 'string', 'max:100'],
                'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
                'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
                'address' => ['sometimes', 'nullable', 'string', 'max:255'],
                'feedback' => ['sometimes', 'nullable', 'string', 'max:1000'],
            ];
        }

        if ($user->role === User::ROLE_MUNICIPAL_MANAGER) {
            return [
                'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
                'status' => ['sometimes', Rule::in(['reported', 'assigned', 'escalated'])],
                'status_note' => ['sometimes', 'nullable', 'string', 'max:1000'],
            ];
        }

        return [
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['sometimes', Rule::in(['reported', 'assigned', 'in_progress', 'resolved', 'escalated'])],
            'status_note' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
