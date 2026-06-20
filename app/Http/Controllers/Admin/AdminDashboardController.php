<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Issue;
use App\Models\Worker;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $issueCounts = [
            'reported' => Issue::query()->where('status', 'reported')->count(),
            'in_progress' => Issue::query()->where('status', 'in_progress')->count(),
            'resolved' => Issue::query()->where('status', 'resolved')->count(),
        ];

        return view('admin.dashboard', [
            'stats' => [
                'workers' => Worker::query()->count(),
                'active_workers' => Worker::query()->where('status', Worker::STATUS_ACTIVE)->count(),
                'departments' => Department::query()->count(),
                'issues' => Issue::query()->count(),
                'reported_issues' => $issueCounts['reported'],
                'in_progress_issues' => $issueCounts['in_progress'],
                'resolved_issues' => $issueCounts['resolved'],
                'overdue_issues' => Issue::query()
                    ->whereNotNull('deadline')
                    ->whereDate('deadline', '<', now()->toDateString())
                    ->where('status', '!=', 'resolved')
                    ->count(),
            ],
            'recentIssues' => Issue::query()
                ->with(['user', 'worker.department'])
                ->latest()
                ->take(6)
                ->get(),
            'topWorkers' => Worker::query()
                ->with('department')
                ->withCount('issues')
                ->orderByDesc('issues_count')
                ->take(5)
                ->get(),
        ]);
    }
}
