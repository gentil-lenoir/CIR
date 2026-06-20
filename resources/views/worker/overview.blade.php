@extends('layouts.worker-frame')

@section('title', 'Worker Overview - CIR')
@section('worker-page', 'overview')

@section('content')
    <section class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary mb-3">Worker Overview</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Daily field operations dashboard</h1>
            <p class="mt-3 max-w-3xl text-slate-600 text-lg leading-8">
                Review assignments, monitor deadlines, and keep citizens updated with live progress from the field.
            </p>
        </div>
        <div class="worker-chart-container min-w-[260px]">
            <p class="text-xs uppercase tracking-[0.22em] text-slate-500 font-bold mb-2">Shift Status</p>
            <div class="flex items-center gap-3">
                <span class="inline-flex h-3 w-3 rounded-full bg-emerald-500"></span>
                <strong class="text-slate-900 text-lg" data-worker-overview-status>Available</strong>
            </div>
            <p class="text-sm text-slate-500 mt-3"><span data-worker-overview-department>Department pending</span></p>
        </div>
    </section>

    <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <article class="worker-stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 mb-3">Open Issues</p>
            <div class="flex items-start justify-between gap-4">
                <h2 class="text-4xl font-extrabold text-slate-900" data-worker-stat="open_issues">0</h2>
                <span class="panel-icon-badge">
                    <img alt="Open issues" src="https://api.iconify.design/material-symbols/assignment-rounded.svg?color=%233b82f6" />
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-4">Assigned complaints still waiting for closure.</p>
        </article>
        <article class="worker-stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 mb-3">In Progress</p>
            <div class="flex items-start justify-between gap-4">
                <h2 class="text-4xl font-extrabold text-slate-900" data-worker-stat="in_progress">0</h2>
                <span class="panel-icon-badge">
                    <img alt="In progress" src="https://api.iconify.design/material-symbols/progress-activity-rounded.svg?color=%2310b981" />
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-4">Work actively being handled right now.</p>
        </article>
        <article class="worker-stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 mb-3">Resolved</p>
            <div class="flex items-start justify-between gap-4">
                <h2 class="text-4xl font-extrabold text-slate-900" data-worker-stat="resolved">0</h2>
                <span class="panel-icon-badge">
                    <img alt="Resolved" src="https://api.iconify.design/material-symbols/task-alt-rounded.svg?color=%239333ea" />
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-4">Completed cases assigned to you.</p>
        </article>
        <article class="worker-stat-card">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500 mb-3">Overdue</p>
            <div class="flex items-start justify-between gap-4">
                <h2 class="text-4xl font-extrabold text-slate-900" data-worker-stat="overdue">0</h2>
                <span class="panel-icon-badge">
                    <img alt="Overdue" src="https://api.iconify.design/material-symbols/warning-rounded.svg?color=%23f97316" />
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-4">Issues whose deadlines already passed.</p>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <article class="worker-chart-container">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">Recent assignments</h2>
                    <p class="text-slate-500 mt-2">Your latest worker queue with live status and deadlines.</p>
                </div>
                <a class="text-sm font-bold text-primary hover:underline" href="{{ route('worker.assigned') }}">View all</a>
            </div>
            <div class="space-y-4" data-worker-recent-assignments>
                <article class="worker-task-card p-5">
                    <p class="text-sm text-slate-500">Loading assignments...</p>
                </article>
            </div>
        </article>

        <article class="worker-chart-container">
            <h2 class="text-2xl font-extrabold text-slate-900 mb-6">Quick actions</h2>
            <div class="grid gap-4">
                <a class="worker-quick-action-btn" href="{{ route('worker.assigned') }}">
                    <img alt="Assigned queue" class="h-12 w-12" src="https://api.iconify.design/material-symbols/assignment-rounded.svg?color=%233b82f6" />
                    <span class="text-base font-bold text-slate-900">Open assigned queue</span>
                </a>
                <a class="worker-quick-action-btn" href="{{ route('worker.in_progress') }}">
                    <img alt="Active work" class="h-12 w-12" src="https://api.iconify.design/material-symbols/build-circle-rounded.svg?color=%2310b981" />
                    <span class="text-base font-bold text-slate-900">Update active work</span>
                </a>
                <a class="worker-quick-action-btn" href="{{ route('worker.performance') }}">
                    <img alt="Performance" class="h-12 w-12" src="https://api.iconify.design/material-symbols/leaderboard-rounded.svg?color=%239333ea" />
                    <span class="text-base font-bold text-slate-900">Review performance</span>
                </a>
            </div>
            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
                <p class="text-sm font-semibold text-slate-500">Due Today</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900" data-worker-stat="due_today">0</p>
            </div>
        </article>
    </section>
@endsection
