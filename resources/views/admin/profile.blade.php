<x-admin-layout
    title="Admin Profile"
    eyebrow="Admin Account"
    heading="Profile"
    subheading="Update your municipal manager identity, contact details, and password from one place."
>
    <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
        <section class="admin-dashboard-surface p-7">
            <div class="flex items-center gap-4">
                <div class="flex h-20 w-20 items-center justify-center rounded-[26px] bg-emerald-100 text-2xl font-black text-emerald-700">
                    {{ str(collect(explode(' ', trim($admin->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'AD')->limit(2, '') }}
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Municipal Manager</p>
                    <h3 class="mt-2 text-2xl font-black text-slate-900">{{ $admin->name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $admin->email }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-3 xl:grid-cols-1">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-3xl font-black text-slate-900">{{ $stats['workers'] }}</p>
                    <p class="mt-2 text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Workers Managed</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-3xl font-black text-slate-900">{{ $stats['departments'] }}</p>
                    <p class="mt-2 text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Departments</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-3xl font-black text-slate-900">{{ $stats['open_issues'] }}</p>
                    <p class="mt-2 text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Open Issues</p>
                </div>
            </div>
        </section>

        <section class="admin-dashboard-surface p-7 md:p-8">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Profile Details</p>
                <h3 class="mt-3 text-2xl font-black text-slate-900">Update account information</h3>
                <p class="mt-2 text-sm text-slate-500">Changes here apply to your admin session, dashboard identity, and future sign-ins.</p>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="grid gap-5 md:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="md:col-span-2">
                    <label class="admin-form-label" for="admin_name">Full Name</label>
                    <input class="admin-form-input" id="admin_name" name="name" type="text" value="{{ old('name', $admin->name) }}" required />
                </div>

                <div>
                    <label class="admin-form-label" for="admin_email">Email Address</label>
                    <input class="admin-form-input" id="admin_email" name="email" type="email" value="{{ old('email', $admin->email) }}" required />
                </div>

                <div>
                    <label class="admin-form-label" for="admin_phone">Phone Number</label>
                    <input class="admin-form-input" id="admin_phone" name="phone" type="text" inputmode="numeric" maxlength="10" value="{{ old('phone', $admin->phone) }}" placeholder="10-digit phone number" />
                </div>

                <div>
                    <label class="admin-form-label" for="admin_password">New Password</label>
                    <input class="admin-form-input" id="admin_password" name="password" type="password" placeholder="Leave blank to keep current password" />
                </div>

                <div>
                    <label class="admin-form-label" for="admin_password_confirmation">Confirm Password</label>
                    <input class="admin-form-input" id="admin_password_confirmation" name="password_confirmation" type="password" />
                </div>

                <div class="md:col-span-2 flex justify-end gap-3 pt-2">
                    <a class="admin-dashboard-btn-secondary" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
                    <button class="admin-dashboard-btn" type="submit">Save Changes</button>
                </div>
            </form>
        </section>
    </div>
</x-admin-layout>
