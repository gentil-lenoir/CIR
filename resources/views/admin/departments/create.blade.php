<x-admin-layout
    title="Create Department"
    eyebrow="Admin / Departments / Create"
    heading="Create New Department"
    subheading="Set up a service department so workers can be assigned accurately and issues can be routed to the right team."
    :header-action='"<div class=\"flex flex-wrap gap-3\"><a href=\"".route("admin.departments.index")."\" class=\"admin-dashboard-btn-secondary\">Back to Departments</a></div>"'
    :sidebar-action='"<p class=\"text-xs font-bold uppercase tracking-[0.28em] text-emerald-300\">Department Setup</p><h3 class=\"mt-3 text-xl font-black text-white\">Create a service unit</h3><p class=\"mt-3 text-sm leading-6 text-slate-300\">Use this form to add a department that workers can join and admins can manage from the dashboard.</p><a href=\"".route("admin.departments.index")."\" class=\"mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 transition hover:bg-emerald-600\">See Department List</a>"'
>
    <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <article class="admin-dashboard-surface p-6 md:p-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Department Form</p>
                <h3 class="mt-3 text-2xl font-black text-slate-900">New department details</h3>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">The department name is required. A department code will be generated automatically from that name when the record is created.</p>
            </div>

            <form action="{{ route('admin.departments.store') }}" class="mt-8 grid gap-5" method="POST">
                @csrf
                @include('admin.departments.partials.form-fields', ['department' => null])
                <div class="flex justify-end gap-3 pt-2">
                    <a class="admin-dashboard-btn-secondary" href="{{ route('admin.departments.index') }}">Cancel</a>
                    <button class="admin-dashboard-btn" type="submit">Create Department</button>
                </div>
            </form>
        </article>

        <article class="admin-dashboard-surface p-6 md:p-8">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Before You Submit</p>
            <div class="mt-6 space-y-5">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h4 class="text-lg font-black text-slate-900">What gets created</h4>
                    <p class="mt-2 text-sm leading-6 text-slate-600">A department record with a unique code, a clear description for admins, and a place where future workers can be assigned.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h4 class="text-lg font-black text-slate-900">Helpful tips</h4>
                    <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                        <li>Use the real civic service name so assignment stays clear for the team.</li>
                        <li>Keep the description focused on the type of issues that department should handle.</li>
                        <li>The system will generate a short department code automatically from the name.</li>
                    </ul>
                </div>
            </div>
        </article>
    </section>
</x-admin-layout>
