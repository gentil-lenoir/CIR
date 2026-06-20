@extends('layouts.worker-frame')

@section('title', 'Worker Settings - CIR')
@section('worker-page', 'settings')

@section('content')
    <section>
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary mb-3">Settings</p>
        <h1 class="text-3xl font-extrabold text-slate-900">Worker preferences</h1>
        <p class="mt-3 text-slate-600 max-w-3xl">Manage operational settings and account preferences.</p>
    </section>

    <div class="hidden" data-worker-settings-alert></div>

    <form class="grid gap-6 lg:grid-cols-2" data-worker-settings-form>
        <article class="worker-settings-card p-6">
            <h2 class="mb-4 text-xl font-extrabold text-slate-900">Appearance</h2>
            <div class="space-y-4">
                <div>
                    <label class="worker-form-label" for="theme_preference">Theme</label>
                    <select class="worker-form-input" id="theme_preference" name="theme_preference">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </select>
                </div>
            </div>
        </article>

        <article class="worker-settings-card p-6">
            <h2 class="mb-4 text-xl font-extrabold text-slate-900">Work Preferences</h2>
            <div class="space-y-4">
                <div>
                    <label class="worker-form-label" for="preferred_zone">Preferred Zone</label>
                    <input class="worker-form-input" id="preferred_zone" name="preferred_zone" type="text" />
                </div>
                <div>
                    <label class="worker-form-label" for="shift_window">Shift Window</label>
                    <input class="worker-form-input" id="shift_window" name="shift_window" type="text" placeholder="09:00 AM - 06:00 PM" />
                </div>
            </div>
        </article>

        <div class="flex justify-end lg:col-span-2">
            <button class="worker-action-btn border-primary/20 bg-primary text-white hover:bg-primary/90" type="submit">Save settings</button>
        </div>
    </form>
@endsection
