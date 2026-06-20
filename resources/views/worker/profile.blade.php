@extends('layouts.worker-frame')

@section('title', 'Worker Profile - CIR')
@section('worker-page', 'profile')

@section('content')
    <section>
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary mb-3">Profile</p>
        <h1 class="text-3xl font-extrabold text-slate-900">Worker identity and assignment profile</h1>
        <p class="mt-3 text-slate-600 max-w-3xl">Keep your field identity details current so assignment routing and coordination stay accurate.</p>
    </section>

    <section class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
        <article class="worker-profile-card p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-100 text-3xl font-extrabold text-emerald-700" data-worker-profile-initials>WK</div>
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900" data-worker-profile-name>Worker</h2>
                    <p class="mt-1 text-slate-500" data-worker-profile-role>Municipal Field Worker</p>
                </div>
            </div>
        </article>

        <article class="worker-profile-card p-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="worker-form-label">Role</p>
                    <p class="font-semibold text-slate-900" data-worker-profile-role>Municipal Field Worker</p>
                </div>
                <div>
                    <p class="worker-form-label">Department</p>
                    <p class="font-semibold text-slate-900" data-worker-profile-department>Not assigned</p>
                </div>
                <div>
                    <p class="worker-form-label">Worker ID</p>
                    <p class="font-semibold text-slate-900" data-worker-profile-code>WK-0000</p>
                </div>
                <div>
                    <p class="worker-form-label">Email</p>
                    <p class="font-semibold text-slate-900" data-worker-profile-email>Not added</p>
                </div>
                <div>
                    <p class="worker-form-label">Phone</p>
                    <p class="font-semibold text-slate-900" data-worker-profile-phone>Not added</p>
                </div>
                <div>
                    <p class="worker-form-label">Status</p>
                    <p class="font-semibold text-emerald-700" data-worker-profile-status>Available</p>
                </div>
                <div>
                    <p class="worker-form-label">Account</p>
                    <p class="font-semibold text-slate-900" data-worker-profile-account-status>Active</p>
                </div>
            </div>
        </article>
    </section>

    <section class="worker-profile-card p-6 md:p-8">
        <h2 class="text-xl font-extrabold text-slate-900">Update profile</h2>
        <p class="mt-2 text-sm text-slate-500">These details are used across assignment, status, and worker-facing records.</p>

        <div class="hidden mt-6" data-worker-profile-alert></div>

        <form class="mt-6 grid gap-5 md:grid-cols-2" data-worker-profile-form>
            <div class="md:col-span-2">
                <label class="worker-form-label" for="worker_name">Full name</label>
                <input class="worker-form-input" id="worker_name" name="name" type="text" />
            </div>
            <div>
                <label class="worker-form-label" for="worker_email">Email address</label>
                <input class="worker-form-input" id="worker_email" name="email" type="email" />
            </div>
            <div>
                <label class="worker-form-label" for="worker_phone">Phone number</label>
                <input class="worker-form-input" id="worker_phone" name="phone" type="text" />
            </div>
            <div class="md:col-span-2">
                <label class="worker-form-label" for="availability_status">Availability status</label>
                <select class="worker-form-input" id="availability_status" name="availability_status">
                    <option value="available">Available</option>
                    <option value="busy">Busy</option>
                    <option value="offline">Offline</option>
                </select>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button class="worker-action-btn border-primary/20 bg-primary text-white hover:bg-primary/90" type="submit">Save profile</button>
            </div>
        </form>
    </section>
@endsection
