<x-admin-layout
    title="Admin Dashboard"
    eyebrow="Municipal Manager"
    heading="Operations Dashboard"
    subheading="Manage departments, assign workers, and keep issue response moving with one admin role only."
>
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <section class="admin-dashboard-surface p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Total Workers</p>
                    <p class="mt-4 text-4xl font-black text-slate-900">{{ $stats['workers'] }}</p>
                    <p class="mt-3 text-sm text-slate-500">{{ $stats['active_workers'] }} active right now</p>
                </div>
                <span class="panel-icon-badge">
                    <img alt="Workers" src="https://api.iconify.design/material-symbols/groups-rounded.svg?color=%2310b981" />
                </span>
            </div>
        </section>
        <section class="admin-dashboard-surface p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Departments</p>
                    <p class="mt-4 text-4xl font-black text-slate-900">{{ $stats['departments'] }}</p>
                    <p class="mt-3 text-sm text-slate-500">Operational teams available for assignment</p>
                </div>
                <span class="panel-icon-badge">
                    <img alt="Departments" src="https://api.iconify.design/material-symbols/apartment-rounded.svg?color=%230ea5e9" />
                </span>
            </div>
        </section>
        <section class="admin-dashboard-surface p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Open Issues</p>
                    <p class="mt-4 text-4xl font-black text-slate-900">{{ $stats['reported_issues'] + $stats['in_progress_issues'] }}</p>
                    <p class="mt-3 text-sm text-slate-500">{{ $stats['resolved_issues'] }} resolved so far</p>
                </div>
                <span class="panel-icon-badge">
                    <img alt="Issues" src="https://api.iconify.design/material-symbols/assignment-late-rounded.svg?color=%2364748b" />
                </span>
            </div>
        </section>
        <section class="admin-dashboard-surface p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Overdue Issues</p>
                    <p class="mt-4 text-4xl font-black text-rose-600">{{ $stats['overdue_issues'] }}</p>
                    <p class="mt-3 text-sm text-slate-500">Past deadline and still not resolved</p>
                </div>
                <span class="panel-icon-badge">
                    <img alt="Overdue issues" src="https://api.iconify.design/material-symbols/warning-rounded.svg?color=%23e11d48" />
                </span>
            </div>
        </section>
    </div>

    <div class="mt-8 grid gap-8 xl:grid-cols-[1.15fr_0.85fr]">
        <section class="admin-dashboard-surface overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h3 class="text-xl font-black text-slate-900">Recent Issues</h3>
                    <p class="mt-1 text-sm text-slate-500">Latest complaints flowing through the admin panel.</p>
                </div>
                <a class="admin-dashboard-btn-secondary" href="{{ route('admin.issues.index') }}">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 font-bold text-slate-500">Issue</th>
                            <th class="px-6 py-4 font-bold text-slate-500">Citizen</th>
                            <th class="px-6 py-4 font-bold text-slate-500">Worker</th>
                            <th class="px-6 py-4 font-bold text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentIssues as $issue)
                            <tr class="border-t border-slate-100">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900">{{ $issue->title }}</p>
                                    <p class="text-xs text-slate-500">{{ $issue->category ?? 'General' }}</p>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $issue->user?->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $issue->worker?->name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-4">
                                    <span class="admin-status-badge admin-status-{{ str_replace('_', '-', $issue->status) }}">{{ str($issue->status)->replace('_', ' ')->title() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="space-y-8">
        <section class="admin-dashboard-surface p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-900">Top Workers</h3>
                    <p class="mt-1 text-sm text-slate-500">Based on assigned issue volume.</p>
                </div>
                <a class="admin-dashboard-btn-secondary" href="{{ route('admin.workers.index') }}">Manage</a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($topWorkers as $worker)
                    <article class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-bold text-slate-900">{{ $worker->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $worker->department?->name ?? 'No department' }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600">{{ $worker->issues_count }} issues</span>
                        </div>
                    </article>
                @empty
                    <p class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-sm text-slate-500">No workers available yet.</p>
                @endforelse
            </div>
        </section>

        <section class="admin-dashboard-surface p-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Admin Details</p>
            <div class="mt-5 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-lg font-black text-slate-900">{{ auth()->user()?->name }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ auth()->user()?->email }}</p>
                <p class="mt-4 text-sm leading-6 text-slate-600">This account controls worker creation, department setup, and issue assignment for the municipal manager dashboard.</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a class="admin-dashboard-btn" href="{{ route('admin.workers.create') }}">Add New Worker</a>
                    <a class="admin-dashboard-btn-secondary" href="{{ route('admin.profile.edit') }}">Edit Profile</a>
                    <a class="admin-dashboard-btn-secondary" href="{{ route('admin.settings.edit') }}">Settings</a>
                </div>
            </div>
        </section>
        </div>
    </div>
</x-admin-layout>
