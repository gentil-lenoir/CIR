<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkerRequest;
use App\Http\Requests\Admin\UpdateWorkerRequest;
use App\Models\Department;
use App\Models\Worker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkerController extends Controller
{
    public function create(): View
    {
        return view('admin.workers.create', [
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $workers = Worker::query()
            ->with('department')
            ->withCount([
                'issues',
                'issues as open_issues_count' => fn ($query) => $query->where('status', '!=', 'resolved'),
                'issues as resolved_issues_count' => fn ($query) => $query->where('status', 'resolved'),
            ])
            ->when($request->filled('department'), fn ($query) => $query->where('department_id', $request->integer('department')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search');

                $query->where(function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.workers.index', [
            'workers' => $workers,
            'departments' => Department::query()->orderBy('name')->get(),
            'stats' => [
                'total' => Worker::query()->count(),
                'active' => Worker::query()->where('status', Worker::STATUS_ACTIVE)->count(),
                'inactive' => Worker::query()->where('status', Worker::STATUS_INACTIVE)->count(),
                'busy' => Worker::query()->where('availability_status', 'busy')->count(),
            ],
        ]);
    }

    public function show(Worker $worker): View
    {
        $worker->load([
            'department',
            'issues.user',
        ])->loadCount([
            'issues',
            'issues as open_issues_count' => fn ($query) => $query->where('status', '!=', 'resolved'),
            'issues as resolved_issues_count' => fn ($query) => $query->where('status', 'resolved'),
        ]);

        $recentIssues = $worker->issues()
            ->with('user')
            ->latest('updated_at')
            ->take(6)
            ->get();

        return view('admin.workers.show', [
            'worker' => $worker,
            'recentIssues' => $recentIssues,
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreWorkerRequest $request): RedirectResponse
    {
        Worker::query()->create($request->validated());

        return redirect()
            ->route('admin.workers.index')
            ->with('success', 'Worker created successfully.');
    }

    public function update(UpdateWorkerRequest $request, Worker $worker): RedirectResponse
    {
        $validated = $request->validated();

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $worker->update($validated);

        return redirect()
            ->route('admin.workers.index')
            ->with('success', 'Worker updated successfully.');
    }

    public function destroy(Worker $worker): RedirectResponse
    {
        DB::transaction(function () use ($worker): void {
            $worker->issues()->update([
                'worker_id' => null,
                'deadline' => null,
                'status' => 'reported',
            ]);

            $worker->delete();
        });

        return redirect()
            ->route('admin.workers.index')
            ->with('success', 'Worker deleted successfully.');
    }

    public function toggleStatus(Worker $worker): RedirectResponse
    {
        $worker->update([
            'status' => $worker->status === Worker::STATUS_ACTIVE
                ? Worker::STATUS_INACTIVE
                : Worker::STATUS_ACTIVE,
        ]);

        return redirect()
            ->route('admin.workers.index')
            ->with('success', 'Worker status updated successfully.');
    }
}
