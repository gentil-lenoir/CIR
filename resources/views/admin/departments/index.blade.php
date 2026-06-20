<x-admin-layout
    title="Departments"
    eyebrow="Admin / Departments"
    heading="Department Management"
    subheading="Create and maintain the service departments that workers belong to."
    :header-action='"<a href=\"".route("admin.departments.create")."\" class=\"admin-dashboard-btn\">Add New Department</a>"'
>
    <section class="admin-dashboard-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-6 py-4 font-bold text-slate-500">Department</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Code</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Workers</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Description</th>
                        <th class="px-6 py-4 font-bold text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                        <tr class="border-t border-slate-100">
                            <td class="px-6 py-5">
                                <p class="font-bold text-slate-900">{{ $department->name }}</p>
                            </td>
                            <td class="px-6 py-5 text-slate-500">{{ $department->code }}</td>
                            <td class="px-6 py-5 text-slate-500">{{ $department->workers_count }}</td>
                            <td class="px-6 py-5 text-slate-500">{{ $department->description ?: 'No description added.' }}</td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-3">
                                    <button class="admin-dashboard-btn-secondary" data-modal-open="edit-department-{{ $department->id }}" type="button">Edit</button>
                                    <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" onsubmit="return confirm('Delete this department?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-10 text-center text-slate-500" colspan="5">No departments available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @foreach ($departments as $department)
        <div class="admin-modal hidden" data-modal id="edit-department-{{ $department->id }}">
            <div class="admin-modal-card">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-black text-slate-900">Edit Department</h3>
                    <button data-modal-close="edit-department-{{ $department->id }}" type="button">Close</button>
                </div>
                <form action="{{ route('admin.departments.update', $department) }}" class="mt-6 space-y-4" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.departments.partials.form-fields', ['department' => $department])
                    <div class="flex justify-end gap-3">
                        <button class="admin-dashboard-btn-secondary" data-modal-close="edit-department-{{ $department->id }}" type="button">Cancel</button>
                        <button class="admin-dashboard-btn" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</x-admin-layout>
