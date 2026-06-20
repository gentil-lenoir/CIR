<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IssueResource;
use App\Models\Department;
use App\Models\Issue;
use App\Models\IssueImage;
use App\Models\IssueStatusHistory;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $worker = $this->worker($request);

        $issues = $worker->issues()
            ->with(['user', 'images', 'worker.department'])
            ->latest('reported_at')
            ->get();

        $openIssues = $issues->where('status', '!=', 'resolved');
        $recentAssignments = $openIssues->take(5)->values();
        $recentResolved = $issues->where('status', 'resolved')->take(5)->values();

        return response()->json([
            'message' => 'Worker dashboard fetched successfully.',
            'data' => [
                'profile' => $this->formatProfile($worker),
                'stats' => [
                    'total_assigned' => $issues->count(),
                    'open_issues' => $openIssues->count(),
                    'in_progress' => $issues->where('status', 'in_progress')->count(),
                    'resolved' => $issues->where('status', 'resolved')->count(),
                    'overdue' => $issues->filter(fn (Issue $issue): bool => $this->isOverdue($issue))->count(),
                    'due_today' => $issues->filter(fn (Issue $issue): bool => $this->isDueToday($issue))->count(),
                ],
                'recent_assignments' => IssueResource::collection($recentAssignments)->resolve(),
                'recent_resolved' => IssueResource::collection($recentResolved)->resolve(),
            ],
        ]);
    }

    public function issues(Request $request): JsonResponse
    {
        $worker = $this->worker($request);
        $view = $request->string('view', 'assigned')->toString();
        $search = $request->string('search')->toString();

        $issues = $worker->issues()
            ->with(['user', 'images', 'worker.department'])
            ->when($view === 'assigned', fn ($query) => $query->where('status', '!=', 'resolved'))
            ->when($view === 'in_progress', fn ($query) => $query->where('status', 'in_progress'))
            ->when($view === 'resolved', fn ($query) => $query->where('status', 'resolved'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($builder) use ($search): void {
                    $builder
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("case when status = 'resolved' then 1 else 0 end")
            ->orderByRaw('deadline is null, deadline asc')
            ->latest('reported_at')
            ->get();

        return response()->json([
            'message' => 'Worker issues fetched successfully.',
            'data' => [
                'issues' => $issues->map(fn (Issue $issue): array => $this->serializeIssue($issue))->values(),
                'counts' => [
                    'assigned' => $worker->issues()->where('status', '!=', 'resolved')->count(),
                    'in_progress' => $worker->issues()->where('status', 'in_progress')->count(),
                    'resolved' => $worker->issues()->where('status', 'resolved')->count(),
                ],
            ],
        ]);
    }

    public function updateIssueStatus(Request $request, Issue $issue): JsonResponse
    {
        $worker = $this->worker($request);

        abort_unless($issue->worker_id === $worker->id, 403, 'You can only update issues assigned to you.');

        $validated = $request->validate([
            'status' => ['required', Rule::in(['in_progress', 'resolved'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'proof_image' => ['nullable', 'image', 'max:5120'],
        ]);

        DB::transaction(function () use ($issue, $validated, $request): void {
            $oldStatus = $issue->status;
            $issue->update([
                'status' => $validated['status'],
            ]);

            if ($request->hasFile('proof_image')) {
                $path = $request->file('proof_image')->store('issue-images', 'public');

                IssueImage::create([
                    'issue_id' => $issue->id,
                    'image_path' => $path,
                    'image_url' => Storage::disk('public')->url($path),
                ]);
            }

            IssueStatusHistory::create([
                'issue_id' => $issue->id,
                'changed_by' => null,
                'from_status' => $oldStatus,
                'to_status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

        });

        $issue = $issue->fresh(['user', 'images', 'worker.department']);

        return response()->json([
            'message' => 'Issue status updated successfully.',
            'data' => [
                'issue' => $this->serializeIssue($issue),
            ],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Worker profile fetched successfully.',
            'data' => [
                'profile' => $this->formatProfile($this->worker($request)),
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $worker = $this->worker($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('workers', 'email')->ignore($worker->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'availability_status' => ['required', Rule::in(['available', 'busy', 'offline'])],
        ]);

        $worker->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => [
                'profile' => $this->formatProfile($worker->fresh('department')),
            ],
        ]);
    }

    public function settings(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Worker settings fetched successfully.',
            'data' => [
                'settings' => $this->formatSettings($this->worker($request)),
            ],
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $worker = $this->worker($request);

        $validated = $request->validate([
            'theme_preference' => ['required', Rule::in(['light', 'dark'])],
            'preferred_zone' => ['nullable', 'string', 'max:255'],
            'shift_window' => ['nullable', 'string', 'max:255'],
        ]);

        $worker->update($validated);

        return response()->json([
            'message' => 'Settings updated successfully.',
            'data' => [
                'settings' => $this->formatSettings($worker->fresh()),
            ],
        ]);
    }

    public function performance(Request $request): JsonResponse
    {
        $worker = $this->worker($request);
        $issues = $worker->issues()->with('statusHistory')->latest('reported_at')->get();
        $resolved = $issues->where('status', 'resolved');
        $totalAssigned = $issues->count();

        $averageResolutionHours = round(
            $resolved
                ->map(function (Issue $issue): ?float {
                    if (! $issue->reported_at || ! $issue->updated_at) {
                        return null;
                    }

                    return $issue->reported_at->diffInMinutes($issue->updated_at, true) / 60;
                })
                ->filter()
                ->avg() ?? 0,
            1
        );

        return response()->json([
            'message' => 'Worker performance fetched successfully.',
            'data' => [
                'performance' => [
                    'summary' => [
                        'total_assigned' => $totalAssigned,
                        'resolved_count' => $resolved->count(),
                        'in_progress_count' => $issues->where('status', 'in_progress')->count(),
                        'assigned_count' => $issues->where('status', '!=', 'resolved')->count(),
                        'completion_rate' => $totalAssigned > 0 ? round(($resolved->count() / $totalAssigned) * 100) : 0,
                        'average_resolution_hours' => $averageResolutionHours,
                        'high_priority_open_count' => $issues->filter(function (Issue $issue): bool {
                            return $issue->status !== 'resolved' && in_array($issue->priority, ['high', 'urgent'], true);
                        })->count(),
                        'started_within_day_count' => $issues->filter(function (Issue $issue): bool {
                            return $issue->status === 'in_progress'
                                && $issue->reported_at
                                && $issue->updated_at
                                && $issue->reported_at->diffInHours($issue->updated_at, true) <= 24;
                        })->count(),
                    ],
                    'recent_resolved' => $resolved
                        ->take(5)
                        ->map(fn (Issue $issue): array => [
                            'id' => $issue->id,
                            'title' => $issue->title,
                            'priority' => $issue->priority,
                            'resolved_at' => optional($issue->updated_at)->toIso8601String(),
                        ])
                        ->values(),
                ],
            ],
        ]);
    }

    private function worker(Request $request): Worker
    {
        $principal = $request->user();

        if ($principal instanceof Worker) {
            $principal->loadMissing('department');

            return $principal;
        }

        if ($principal instanceof User && $principal->role === User::ROLE_WORKER) {
            $worker = Worker::query()->where('email', $principal->email)->first();

            if ($worker) {
                return $worker->load('department');
            }

            $departmentId = $principal->getAttribute('department_id')
                ?? Department::query()->value('id');

            $worker = tap(new Worker(['email' => $principal->email]), function (Worker $worker) use ($principal, $departmentId): void {
                $worker->fill([
                    'name' => $principal->name,
                    'phone' => $principal->phone,
                    'department_id' => $departmentId,
                    'password' => $principal->getAuthPassword(),
                    'status' => Worker::STATUS_ACTIVE,
                    'availability_status' => $principal->availability_status ?? 'available',
                    'theme_preference' => $principal->theme_preference ?? 'light',
                    'preferred_zone' => $principal->preferred_zone,
                    'shift_window' => $principal->shift_window,
                ]);
                $worker->save();
            })->load('department');

            return $worker instanceof Worker ? $worker : abort(403, 'Unable to prepare worker profile.');
        }

        abort(403, 'The authenticated account is not a worker profile.');
    }

    private function formatProfile(Worker $worker): array
    {
        return [
            'id' => $worker->id,
            'worker_code' => 'WK-'.str_pad((string) $worker->id, 4, '0', STR_PAD_LEFT),
            'name' => $worker->name,
            'email' => $worker->email,
            'phone' => $worker->phone,
            'department' => $worker->department?->name,
            'account_status' => $worker->status,
            'availability_status' => $worker->availability_status ?? 'available',
            'role_label' => 'Municipal Field Worker',
            'initials' => collect(explode(' ', trim($worker->name)))
                ->filter()
                ->take(2)
                ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
                ->implode(''),
            'stats' => [
                'assigned' => $worker->issues()->where('status', '!=', 'resolved')->count(),
                'resolved' => $worker->issues()->where('status', 'resolved')->count(),
            ],
        ];
    }

    private function formatSettings(Worker $worker): array
    {
        return [
            'theme_preference' => $worker->theme_preference ?? 'light',
            'preferred_zone' => $worker->preferred_zone,
            'shift_window' => $worker->shift_window,
        ];
    }

    private function serializeIssue(Issue $issue): array
    {
        $payload = (new IssueResource($issue))->resolve();
        $payload['is_overdue'] = $this->isOverdue($issue);
        $payload['is_due_today'] = $this->isDueToday($issue);
        $payload['maps_url'] = $issue->latitude && $issue->longitude
            ? 'https://www.google.com/maps?q='.$issue->latitude.','.$issue->longitude
            : null;

        return $payload;
    }

    private function isOverdue(Issue $issue): bool
    {
        return $issue->status !== 'resolved'
            && $issue->deadline !== null
            && $issue->deadline->isPast()
            && ! $issue->deadline->isToday();
    }

    private function isDueToday(Issue $issue): bool
    {
        return $issue->status !== 'resolved'
            && $issue->deadline !== null
            && $issue->deadline->isToday();
    }
}
