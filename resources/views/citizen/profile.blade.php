@extends('layouts.citizen-page')

@section('title', 'Citizen Profile | CIR')
@section('citizen-page', 'profile')
@section('html-class', 'light')
@section('body-class', 'dashcode-page font-display')

@section('content')
    <header class="dashcode-topbar">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('citizen.dashboard') }}" class="flex items-center gap-3">
                <div class="bg-primary p-2 rounded-xl shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined text-white text-2xl block">account_circle</span>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-navy-400">Citizen View</p>
                    <h1 class="text-xl font-bold text-navy-900">Profile</h1>
                </div>
            </a>
        </div>
    </header>

    <main class="dashcode-shell max-w-6xl">
        <div class="grid grid-cols-1 xl:grid-cols-[360px_minmax(0,1fr)] gap-6">
            <section class="dashcode-panel p-8 text-center">
                <div class="w-28 h-28 rounded-[28px] bg-emerald-100 text-emerald-700 mx-auto ring-4 ring-white shadow-lg flex items-center justify-center text-4xl font-black">CF</div>
                <h2 class="text-2xl font-extrabold text-navy-900 mt-5" data-profile-name>Citizen</h2>
                <p class="text-navy-500 mt-1" data-profile-email>citizen@example.com</p>

                <div class="grid grid-cols-2 gap-4 mt-8">
                    <div class="dashcode-surface p-4">
                        <p class="text-3xl font-extrabold text-primary" data-profile-total>0</p>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-navy-400 mt-2">Reported</p>
                    </div>
                    <div class="dashcode-surface p-4">
                        <p class="text-3xl font-extrabold text-primary" data-profile-resolved>0</p>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-navy-400 mt-2">Resolved</p>
                    </div>
                </div>
            </section>

            <section class="space-y-6">
                <div class="dashcode-panel p-8">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="dashcode-icon-chip">
                            <span class="material-symbols-outlined text-2xl">person</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-navy-900">Personal Information</h3>
                            <p class="text-sm text-navy-500">Keep your civic reporting profile up to date.</p>
                        </div>
                    </div>

                    <div class="hidden mb-6" data-profile-message></div>

                    <form class="grid grid-cols-1 md:grid-cols-2 gap-6" data-citizen-profile-form>
                        <div>
                            <label class="dashcode-label">Full Name</label>
                            <input class="dashcode-input" name="name" type="text" />
                        </div>
                        <div>
                            <label class="dashcode-label">Email Address</label>
                            <input class="dashcode-input" name="email" type="email" />
                        </div>
                        <div>
                            <label class="dashcode-label">Mobile Number</label>
                            <input class="dashcode-input" name="phone" type="tel" />
                        </div>
                        <div>
                            <label class="dashcode-label">Preferred Location</label>
                            <input class="dashcode-input" name="preferred_location" type="text" />
                        </div>
                        <div>
                            <label class="dashcode-label">New Password</label>
                            <input class="dashcode-input" name="password" type="password" />
                        </div>
                        <div>
                            <label class="dashcode-label">Confirm Password</label>
                            <input class="dashcode-input" name="password_confirmation" type="password" />
                        </div>
                        <div class="md:col-span-2 flex justify-end gap-4 pt-2">
                            <button class="dashcode-btn-secondary" type="reset">Reset</button>
                            <button class="dashcode-btn-primary" type="submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>
@endsection
