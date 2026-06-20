<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Issue;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function stats(): JsonResponse
    {
        $workers = Worker::count();
        $activeWorkers = Worker::where('status', 'active')->count();
        $departments = Department::count();
        $reportedIssues = Issue::where('status', 'reported')->count();
        $inProgressIssues = Issue::where('status', 'in_progress')->count();
        $resolvedIssues = Issue::where('status', 'resolved')->count();
        $overdueIssues = Issue::whereIn('status', ['reported', 'in_progress'])
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'workers' => $workers,
                'active_workers' => $activeWorkers,
                'departments' => $departments,
                'reported_issues' => $reportedIssues,
                'in_progress_issues' => $inProgressIssues,
                'resolved_issues' => $resolvedIssues,
                'overdue_issues' => $overdueIssues,
            ],
        ]);
    }

    public function issues(): JsonResponse
    {
        $issues = Issue::with('worker.department')
            ->latest()
            ->paginate(50);

        $data = $issues->map(function ($issue) {
            return [
                'id' => $issue->id,
                'title' => $issue->title,
                'category' => $issue->category,
                'status' => $issue->status,
                'worker_name' => $issue->worker?->name ?? 'Unassigned',
                'department_name' => $issue->worker?->department?->name,
                'created_at' => $issue->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'issues' => $data,
                'total' => $issues->total(),
                'page' => $issues->currentPage(),
                'limit' => $issues->perPage(),
            ],
        ]);
    }

    public function recentIssues(): JsonResponse
    {
        $issues = Issue::with('worker.department')
            ->latest()
            ->limit(10)
            ->get();

        $data = $issues->map(function ($issue) {
            return [
                'id' => $issue->id,
                'title' => $issue->title,
                'category' => $issue->category,
                'status' => $issue->status,
                'worker_name' => $issue->worker?->name ?? 'Unassigned',
                'department_name' => $issue->worker?->department?->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function topWorkers(): JsonResponse
    {
        $workers = Worker::withCount('issues')
            ->with('department')
            ->orderByDesc('issues_count')
            ->limit(5)
            ->get();

        $data = $workers->map(function ($worker) {
            return [
                'id' => $worker->id,
                'name' => $worker->name,
                'department_name' => $worker->department?->name ?? 'No dept',
                'issues_count' => $worker->issues_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function workers(Request $request): JsonResponse
    {
        $query = Worker::with('department');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $page = max(1, (int) $request->integer('page', 1));
        $limit = max(1, min(100, (int) $request->integer('limit', 50)));
        $total = $query->count();
        $workers = $query->skip(($page - 1) * $limit)->take($limit)->get();

        $data = $workers->map(function ($worker) {
            return [
                'id' => $worker->id,
                'name' => $worker->name,
                'email' => $worker->email,
                'phone' => $worker->phone,
                'department_id' => $worker->department_id,
                'department_name' => $worker->department?->name,
                'status' => $worker->status,
                'availability_status' => $worker->availability_status ?? 'available',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'workers' => $data,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
            ],
        ]);
    }

    public function storeWorker(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('workers', 'email')],
            'phone' => ['nullable', 'string', 'max:25'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        $worker = Worker::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'password' => $validated['password'] ?? Hash::make('password'),
            'status' => 'active',
            'availability_status' => 'available',
        ]);

        return response()->json([
            'success' => true,
            'data' => ['id' => $worker->id],
        ]);
    }

    public function updateWorker(Request $request, int $id): JsonResponse
    {
        $worker = Worker::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('workers', 'email')->ignore($worker->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
            'availability_status' => ['sometimes', Rule::in(['available', 'busy', 'offline'])],
        ]);

        $worker->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroyWorker(int $id): JsonResponse
    {
        $worker = Worker::findOrFail($id);
        $worker->delete();

        return response()->json(['success' => true]);
    }

    public function departments(): JsonResponse
    {
        $departments = Department::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $departments,
        ]);
    }

    public function storeDepartment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $department = Department::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => ['id' => $department->id],
        ]);
    }

    public function updateDepartment(Request $request, int $id): JsonResponse
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $department->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroyDepartment(int $id): JsonResponse
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json(['success' => true]);
    }
}
