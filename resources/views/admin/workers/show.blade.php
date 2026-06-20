<x-admin-layout
    title="Worker Details"
    eyebrow="Admin / Workers / Details"
    heading="{{ $worker->name }}"
    subheading="Review the full worker account, department assignment, preferences, and recent workload activity."
    :header-action="'<div class=&quot;flex flex-wrap gap-3&quot;><a href=&quot;'.route('admin.workers.index').'&quot; class=&quot;admin-dashboard-btn-secondary&quot;>Back to Workers</a><a href=&quot;'.route('admin.workers.create').'&quot; class=&quot;admin-dashboard-btn&quot;>Add New Worker</a></div>'"
    :sidebar-action="'<p class=&quot;text-xs font-bold uppercase tracking-[0.28em] text-emerald-300&quot;>Worker Details</p><h3 class=&quot;mt-3 text-xl font-black text-white&quot;>'.e($worker->name).'</h3><p class=&quot;mt-3 text-sm leading-6 text-slate-300&quot;>Open the worker list anytime to create more accounts or rebalance departments.</p><a href=&quot;'.route('admin.workers.index').'&quot; class=&quot;mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-white/10 px-4 py-3 text-sm font-bold text-white transition hover:bg-white/15&quot;>Manage Worker List</a>'"
>
    <section class="grid gap-6 xl:grid-cols-[1fr_0.9fr]">
        <article class="admin-dashboard-surface p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Worker Identity</p>
                    <h3 class="mt-3 text-3xl font-black text-slate-900">{{ $worker->name }}</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ $worker->email }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $worker->phone ?: 'Phone not added yet' }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="admin-status-badge {{ $worker->status === 'active' ? 'admin-status-resolved' : 'admin-status-reported' }}">{{ ucfirst($worker->status) }}</span>
                    <span class="admin-status-badge {{ $worker->availability_status === 'available' ? 'admin-status-resolved' : ($worker->availability_status === 'busy' ? 'admin-status-in-progress' : 'admin-status-reported') }}">
                        {{ str($worker->availability_status)->replace('_', ' ')->title() }}
                    </span>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Department</p>
                    <p class="mt-3 text-lg font-black text-slate-900">{{ $worker->department?->name }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Theme Preference</p>
                    <p class="mt-3 text-lg font-black text-slate-900">{{ ucfirst($worker->theme_preference ?? 'light') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Preferred Zone</p>
                    <p class="mt-3 text-lg font-black text-slate-900">{{ $worker->preferred_zone ?: 'Not set' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Shift Window</p>
                    <p class="mt-3 text-lg font-black text-slate-900">{{ $worker->shift_window ?: 'Not set' }}</p>
                </div>
            </div>
        </article>

        <article class="admin-dashboard-surface p-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Workload Summary</p>
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-500">Total Assigned</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $worker->issues_count }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-500">Open Issues</p>
                    <p class="mt-3 text-3xl font-black text-sky-600">{{ $worker->open_issues_count }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-500">Resolved</p>
                    <p class="mt-3 text-3xl font-black text-emerald-600">{{ $worker->resolved_issues_count }}</p>
                </div>
            </div>
        </article>
    </section>

    <section class="admin-dashboard-surface mt-6 p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Account Actions</p>
                <h3 class="mt-2 text-2xl font-black text-slate-900">Manage this worker account</h3>
            </div>
            <div class="flex flex-wrap gap-3">
                <form action="{{ route('admin.workers.toggle-status', $worker) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button class="admin-dashboard-btn-secondary" type="submit">{{ $worker->status === 'active' ? 'Deactivate Account' : 'Activate Account' }}</button>
                </form>
                <button class="admin-dashboard-btn" data-modal-open="edit-worker-{{ $worker->id }}" type="button">Edit Worker Details</button>
            </div>
        </div>
    </section>

    <section class="admin-dashboard-surface mt-6 overflow-hidden">
        <div class="px-6 py-5">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Recent Work</p>
            <h3 class="mt-2 text-2xl font-black text-slate-900">Latest issue activity for this worker</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-6 py-4 font-bold text-slate-500">Issue</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Citizen</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Priority</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Status</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentIssues as $issue)
                        <tr class="border-t border-slate-100">
                            <td class="px-6 py-5">
                                <p class="font-bold text-slate-900">{{ $issue->title }}</p>
                                <p class="text-xs text-slate-500">{{ $issue->address }}</p>
                            </td>
                            <td class="px-6 py-5 text-slate-500">{{ $issue->user?->name }}</td>
                            <td class="px-6 py-5 text-slate-500">{{ str($issue->priority)->title() }}</td>
                            <td class="px-6 py-5">
                                <span class="admin-status-badge {{ $issue->status === 'resolved' ? 'admin-status-resolved' : ($issue->status === 'in_progress' ? 'admin-status-in-progress' : 'admin-status-reported') }}">
                                    {{ str($issue->status)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-slate-500">{{ $issue->deadline?->format('d M Y') ?: 'Not set' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-10 text-center text-slate-500" colspan="5">No issues assigned to this worker yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="admin-modal hidden" data-modal id="edit-worker-{{ $worker->id }}">
        <div class="admin-modal-card">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-black text-slate-900">Edit Worker Account</h3>
                    <p class="mt-2 text-sm text-slate-500">Update worker identity, department placement, and account preferences.</p>
                </div>
                <button class="text-sm font-bold text-slate-500" data-modal-close="edit-worker-{{ $worker->id }}" type="button">Close</button>
            </div>
            <form action="{{ route('admin.workers.update', $worker) }}" class="mt-6 grid gap-4 md:grid-cols-2" method="POST">
                @csrf
                @method('PUT')
                @include('admin.workers.partials.form-fields', ['worker' => $worker])
                <div class="md:col-span-2 flex justify-end gap-3">
                    <button class="admin-dashboard-btn-secondary" data-modal-close="edit-worker-{{ $worker->id }}" type="button">Cancel</button>
                    <button class="admin-dashboard-btn" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
