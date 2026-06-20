<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignIssueRequest;
use App\Models\Department;
use App\Models\Issue;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminIssueController extends Controller
{
    public function index(Request $request): View
    {
        $issues = Issue::query()
            ->with(['user', 'worker.department'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('department'), function ($query) use ($request): void {
                $query->whereHas('worker', fn ($workerQuery) => $workerQuery->where('department_id', $request->integer('department')));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.issues.index', [
            'issues' => $issues,
            'departments' => Department::query()->with('workers')->orderBy('name')->get(),
            'statusOptions' => [
                'reported' => 'Reported',
                'in_progress' => 'In Progress',
                'resolved' => 'Resolved',
            ],
        ]);
    }

    public function update(Request $request, Issue $issue): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['reported', 'in_progress', 'resolved'])],
        ]);

        if ($validated['status'] === 'reported') {
            $issue->update([
                'status' => 'reported',
                'worker_id' => null,
                'deadline' => null,
            ]);
        } else {
            $issue->update($validated);
        }

        return redirect()
            ->route('admin.issues.index')
            ->with('success', 'Issue status updated successfully.');
    }

    public function assign(AssignIssueRequest $request, Issue $issue): RedirectResponse
    {
        $worker = Worker::query()->findOrFail($request->integer('worker_id'));

        $issue->update([
            'worker_id' => $worker->id,
            'deadline' => $request->date('deadline')?->toDateString(),
            'status' => $request->string('status', 'in_progress')->toString(),
        ]);

        return redirect()
            ->route('admin.issues.index')
            ->with('success', 'Issue assigned successfully.');
    }

    public function workersByDepartment(Department $department): JsonResponse
    {
        return response()->json([
            'data' => $department->workers()
                ->where('status', Worker::STATUS_ACTIVE)
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }
}
