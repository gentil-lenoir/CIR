<x-admin-layout title="Issues" eyebrow="Admin / Issues" heading="Issue Assignment Board" subheading="Assign workers, track deadlines, and update issue progress from one queue.">
    <section class="admin-dashboard-surface p-4 sm:p-6">
        <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" method="GET">
            <select class="admin-form-input" name="status">
                <option value="">All statuses</option>
                @foreach ($statusOptions as $value => $label)
                    <option @selected(request('status') === $value) value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <select class="admin-form-input" name="department">
                <option value="">All departments</option>
                @foreach ($departments as $department)
                    <option @selected((string) request('department') === (string) $department->id) value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
            <button class="admin-dashboard-btn w-full justify-center md:w-auto" type="submit">Apply Filters</button>
        </form>
    </section>

    <section class="admin-dashboard-surface mt-6 overflow-hidden">
        <div class="safe-scroll-x">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-6 py-4 font-bold text-slate-500">Issue</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Citizen</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Department</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Worker</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Deadline</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Status</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($issues as $issue)
                        @php
                            $overdue = $issue->deadline && $issue->deadline->isPast() && $issue->status !== 'resolved';
                        @endphp
                        <tr class="border-t border-slate-100 {{ $overdue ? 'bg-rose-50/70' : '' }}">
                            <td class="px-6 py-5">
                                <p class="font-bold text-slate-900">{{ $issue->title }}</p>
                                <p class="text-xs text-slate-500">{{ $issue->category ?? 'General' }}</p>
                            </td>
                            <td class="px-6 py-5 text-slate-500">{{ $issue->user?->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-5 text-slate-500">{{ $issue->worker?->department?->name ?? 'Not selected' }}</td>
                            <td class="px-6 py-5 text-slate-500">{{ $issue->worker?->name ?? 'Unassigned' }}</td>
                            <td class="px-6 py-5">
                                @if ($issue->deadline)
                                    <span class="{{ $overdue ? 'font-bold text-rose-600' : 'text-slate-500' }}">{{ $issue->deadline->format('d M Y') }}</span>
                                @else
                                    <span class="text-slate-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <span class="admin-status-badge admin-status-{{ str_replace('_', '-', $issue->status) }}">{{ $statusOptions[$issue->status] ?? str($issue->status)->replace('_', ' ')->title() }}</span>
                            </td>
                            <td class="px-6 py-5 min-w-52">
                                <div class="flex flex-wrap gap-3">
                                    <button class="admin-dashboard-btn" data-modal-open="assign-issue-{{ $issue->id }}" type="button">Assign Worker</button>
                                    <form action="{{ route('admin.issues.update', $issue) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input name="status" type="hidden" value="{{ $issue->status === 'resolved' ? 'reported' : 'resolved' }}" />
                                        <button class="admin-dashboard-btn-secondary" type="submit">{{ $issue->status === 'resolved' ? 'Reopen' : 'Mark Resolved' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-10 text-center text-slate-500" colspan="7">No issues found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            {{ $issues->links() }}
        </div>
    </section>

    @foreach ($issues as $issue)
        <div class="admin-modal hidden" data-modal id="assign-issue-{{ $issue->id }}">
            <div class="admin-modal-card" data-worker-filter>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900">Assign Worker</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $issue->title }}</p>
                    </div>
                    <button class="admin-dashboard-btn-secondary" data-modal-close="assign-issue-{{ $issue->id }}" type="button">Close</button>
                </div>

                <form action="{{ route('admin.issues.assign', $issue) }}" class="mt-6 grid gap-4 md:grid-cols-2" method="POST">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="admin-form-label" for="department-{{ $issue->id }}">Department</label>
                        <select class="admin-form-input" data-department-select id="department-{{ $issue->id }}" name="department_id" required>
                            @foreach ($departments as $department)
                                <option @selected($issue->worker?->department_id === $department->id || (!$issue->worker && $loop->first)) value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="admin-form-label" for="worker-{{ $issue->id }}">Worker</label>
                        <select class="admin-form-input" data-worker-select id="worker-{{ $issue->id }}" name="worker_id" required>
                            <option value="">Select worker</option>
                            @foreach ($departments as $department)
                                @foreach ($department->workers->where('status', 'active') as $worker)
                                    <option
                                        @selected($issue->worker_id === $worker->id)
                                        data-department-id="{{ $department->id }}"
                                        value="{{ $worker->id }}"
                                    >
                                        {{ $worker->name }} ({{ $worker->email }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="admin-form-label" for="deadline-{{ $issue->id }}">Deadline</label>
                        <input class="admin-form-input" id="deadline-{{ $issue->id }}" min="{{ now()->toDateString() }}" name="deadline" required type="date" value="{{ optional($issue->deadline)->toDateString() }}" />
                    </div>
                    <div>
                        <label class="admin-form-label" for="status-{{ $issue->id }}">Status</label>
                        <select class="admin-form-input" id="status-{{ $issue->id }}" name="status">
                            <option value="in_progress">In Progress</option>
                            <option @selected($issue->status === 'reported') value="reported">Reported</option>
                            <option @selected($issue->status === 'resolved') value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button class="admin-dashboard-btn-secondary" data-modal-close="assign-issue-{{ $issue->id }}" type="button">Cancel</button>
                        <button class="admin-dashboard-btn" type="submit">Save Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</x-admin-layout>
