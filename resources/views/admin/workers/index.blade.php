<x-admin-layout
    title="Workers"
    eyebrow="Admin / Workers"
    heading="Worker Management"
    subheading="Create complete worker accounts, review workload, and manage worker availability from one place."
    :sidebar-action="'<p class=&quot;text-xs font-bold uppercase tracking-[0.28em] text-emerald-300&quot;>Worker Tools</p><h3 class=&quot;mt-3 text-xl font-black text-white&quot;>Create and manage worker accounts</h3><p class=&quot;mt-3 text-sm leading-6 text-slate-300&quot;>Open the full worker signup form, assign the right department, and keep account preferences visible for supervisors.</p><a href=&quot;'.route('admin.workers.create').'&quot; class=&quot;mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 transition hover:bg-emerald-600&quot;>Add New Worker</a>'"
>
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="admin-dashboard-surface p-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Total Workers</p>
            <h3 class="mt-3 text-4xl font-black text-slate-900">{{ $stats['total'] }}</h3>
        </article>
        <article class="admin-dashboard-surface p-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Active Accounts</p>
            <h3 class="mt-3 text-4xl font-black text-emerald-600">{{ $stats['active'] }}</h3>
        </article>
        <article class="admin-dashboard-surface p-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Inactive Accounts</p>
            <h3 class="mt-3 text-4xl font-black text-amber-600">{{ $stats['inactive'] }}</h3>
        </article>
        <article class="admin-dashboard-surface p-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Busy In Field</p>
            <h3 class="mt-3 text-4xl font-black text-sky-600">{{ $stats['busy'] }}</h3>
        </article>
    </section>

    <section class="admin-dashboard-surface mt-6 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <form class="grid gap-4 md:grid-cols-4 lg:flex-1" method="GET">
                <div class="panel-search-wrap">
                    <img alt="Search" src="https://api.iconify.design/material-symbols/search-rounded.svg?color=%2364748b" />
                    <input class="admin-form-input" name="search" placeholder="Search workers" type="text" value="{{ request('search') }}" />
                </div>
                <select class="admin-form-input" name="department">
                    <option value="">All departments</option>
                    @foreach ($departments as $department)
                        <option @selected((string) request('department') === (string) $department->id) value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                <select class="admin-form-input" name="status">
                    <option value="">All statuses</option>
                    <option @selected(request('status') === 'active') value="active">Active</option>
                    <option @selected(request('status') === 'inactive') value="inactive">Inactive</option>
                </select>
                <button class="admin-dashboard-btn" type="submit">Apply Filters</button>
            </form>
            <a class="admin-dashboard-btn w-full justify-center lg:w-auto" href="{{ route('admin.workers.create') }}">Add New Worker</a>
        </div>
    </section>

    <section class="admin-dashboard-surface mt-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-6 py-4 font-bold text-slate-500">Worker</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Department</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Availability</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Workload</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Theme</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Account</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workers as $worker)
                        <tr class="border-t border-slate-100">
                            <td class="px-6 py-5">
                                <p class="font-bold text-slate-900">{{ $worker->name }}</p>
                                <p class="text-xs text-slate-500">{{ $worker->email }}</p>
                                <p class="mt-2 text-xs text-slate-400">{{ $worker->phone ?: 'Phone not added yet' }}</p>
                            </td>
                            <td class="px-6 py-5 text-slate-500">{{ $worker->department?->name }}</td>
                            <td class="px-6 py-5">
                                <span class="admin-status-badge {{ $worker->availability_status === 'available' ? 'admin-status-resolved' : ($worker->availability_status === 'busy' ? 'admin-status-in-progress' : 'admin-status-reported') }}">
                                    {{ str($worker->availability_status)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-slate-500">
                                <p class="font-semibold text-slate-900">{{ $worker->open_issues_count }} open</p>
                                <p class="text-xs text-slate-500">{{ $worker->resolved_issues_count }} resolved</p>
                            </td>
                            <td class="px-6 py-5 text-slate-500">{{ ucfirst($worker->theme_preference ?? 'light') }}</td>
                            <td class="px-6 py-5">
                                <span class="admin-status-badge {{ $worker->status === 'active' ? 'admin-status-resolved' : 'admin-status-reported' }}">{{ ucfirst($worker->status) }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-3">
                                    <a class="admin-dashboard-btn-secondary" href="{{ route('admin.workers.show', $worker) }}">View Details</a>
                                    <button class="admin-dashboard-btn-secondary" data-modal-open="edit-worker-{{ $worker->id }}" type="button">Edit</button>
                                    <form action="{{ route('admin.workers.toggle-status', $worker) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="admin-dashboard-btn-secondary" type="submit">{{ $worker->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                    <form action="{{ route('admin.workers.destroy', $worker) }}" method="POST" onsubmit="return confirm('Delete this worker account? Open issues will return to reported status.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-10 text-center text-slate-500" colspan="7">No workers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-6 py-4">
            {{ $workers->links() }}
        </div>
    </section>

    @foreach ($workers as $worker)
        <div class="admin-modal hidden" data-modal id="edit-worker-{{ $worker->id }}">
            <div class="admin-modal-card">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900">Edit Worker Account</h3>
                        <p class="mt-2 text-sm text-slate-500">Update identity details, work preferences, and theme settings.</p>
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
    @endforeach
</x-admin-layout>
