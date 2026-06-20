<x-admin-layout
    title="Create Worker"
    eyebrow="Admin / Workers / Create"
    heading="Worker Signup Form"
    subheading="Create a complete worker account with identity, department, work preferences, notification settings, and login credentials."
    :header-action="'<div class=&quot;flex flex-wrap gap-3&quot;><a href=&quot;'.route('admin.workers.index').'&quot; class=&quot;admin-dashboard-btn-secondary&quot;>Back to Worker List</a></div>'"
    :sidebar-action="'<p class=&quot;text-xs font-bold uppercase tracking-[0.28em] text-emerald-300&quot;>Worker Signup</p><h3 class=&quot;mt-3 text-xl font-black text-white&quot;>Add a new worker account</h3><p class=&quot;mt-3 text-sm leading-6 text-slate-300&quot;>Use this signup form to create a worker login with all details the field team needs.</p><a href=&quot;'.route('admin.workers.index').'&quot; class=&quot;mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-white/10 px-4 py-3 text-sm font-bold text-white transition hover:bg-white/15&quot;>See Worker List</a>'"
>
    <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <article class="admin-dashboard-surface p-6 md:p-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">New Worker Account</p>
                <h3 class="mt-3 text-2xl font-black text-slate-900">Worker signup details</h3>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Every field below connects directly to the worker dashboard, issue routing, and worker login experience.</p>
            </div>

            <form action="{{ route('admin.workers.store') }}" class="mt-8 grid gap-4 md:grid-cols-2" method="POST">
                @csrf
                @include('admin.workers.partials.form-fields', ['worker' => null])
                <div class="md:col-span-2 flex justify-end gap-3 pt-2">
                    <a class="admin-dashboard-btn-secondary" href="{{ route('admin.workers.index') }}">Cancel</a>
                    <button class="admin-dashboard-btn" type="submit">Create Worker Account</button>
                </div>
            </form>
        </article>

        <article class="admin-dashboard-surface p-6 md:p-8">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Before You Submit</p>
            <div class="mt-6 space-y-5">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h4 class="text-lg font-black text-slate-900">What this creates</h4>
                    <p class="mt-2 text-sm leading-6 text-slate-600">A real worker login account, a department assignment, dashboard preferences, theme preference, and notification defaults for that worker.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h4 class="text-lg font-black text-slate-900">Recommended details</h4>
                    <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                        <li>Use a real department so issue assignment stays accurate.</li>
                        <li>Set availability correctly so admins can see if the worker is free or busy.</li>
                        <li>Choose light or dark theme depending on device preference in the field.</li>
                        <li>Set the worker account preferences so the field panel is ready to use immediately.</li>
                    </ul>
                </div>
            </div>
        </article>
    </section>
</x-admin-layout>
