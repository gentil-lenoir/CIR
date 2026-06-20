@extends('layouts.worker-shell')

@section('title', 'Worker Dashboard - CIR')
@section('worker-page', 'shell')

@section('content')
    <div class="portal-drawer-backdrop hidden" data-drawer-backdrop="worker-sidebar"></div>
    <div class="min-h-screen lg:flex">
        <aside class="portal-mobile-sidebar is-closed w-72 bg-navy-950 text-white flex-shrink-0 flex flex-col h-screen z-50" id="worker-sidebar">
            <div class="p-6">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-[52px] w-[52px] items-center justify-center rounded-2xl bg-linear-to-b from-[#21d4b4] to-primary text-white shadow-[0_18px_36px_rgba(16,185,129,0.28)]">
                            <span class="material-symbols-outlined text-[30px] leading-none">location_city</span>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[1.45rem] font-black leading-none tracking-[-0.05em] text-white"><span class="text-primary">CIR</span></p>
                            <p class="mt-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-300">Smart Public Issue Reporting</p>
                        </div>
                    </div>
                    <button class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white lg:hidden" data-drawer-close="worker-sidebar" type="button">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <a class="citizen-sidebar-link active" href="{{ route('worker.overview') }}" target="contentFrame" data-worker-nav>
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium">Overview</span>
            </a>
            <a class="citizen-sidebar-link" href="{{ route('worker.assigned') }}" target="contentFrame" data-worker-nav>
                <span class="material-symbols-outlined">assignment</span>
                <span class="font-medium">Assigned Issues</span>
            </a>
            <a class="citizen-sidebar-link" href="{{ route('worker.in_progress') }}" target="contentFrame" data-worker-nav>
                <span class="material-symbols-outlined">pending_actions</span>
                <span class="font-medium">In Progress</span>
            </a>
            <a class="citizen-sidebar-link" href="{{ route('worker.resolved') }}" target="contentFrame" data-worker-nav>
                <span class="material-symbols-outlined">task_alt</span>
                <span class="font-medium">Resolved</span>
            </a>

            <div class="pt-4 pb-2 px-4">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Account</span>
            </div>

            <a class="citizen-sidebar-link" href="{{ route('worker.performance') }}" target="contentFrame" data-worker-nav>
                <span class="material-symbols-outlined">leaderboard</span>
                <span class="font-medium">Performance</span>
            </a>
            <a class="citizen-sidebar-link" href="{{ route('worker.profile') }}" target="contentFrame" data-worker-nav>
                <span class="material-symbols-outlined">person</span>
                <span class="font-medium">Profile</span>
            </a>
            <a class="citizen-sidebar-link" href="{{ route('worker.settings') }}" target="contentFrame" data-worker-nav>
                <span class="material-symbols-outlined">settings</span>
                <span class="font-medium">Settings</span>
            </a>
            </nav>

            <div class="p-4 border-t border-white/10">
                <div class="rounded-3xl border border-white/10 bg-white/5 px-4 py-4 mb-4">
                    <p class="text-xs uppercase tracking-[0.28em] text-emerald-300 font-bold">Worker</p>
                    <p class="mt-3 text-base font-bold text-white" data-worker-shell-name>Worker</p>
                    <p class="mt-1 text-sm text-slate-400" data-worker-shell-email>worker@example.com</p>
                    <p class="mt-3 text-xs font-bold uppercase tracking-[0.18em] text-primary" data-worker-shell-department>Department</p>
                </div>
                <a class="citizen-sidebar-link text-red-400 hover:text-red-300 hover:bg-red-500/10" href="{{ route('login') }}" data-auth-logout data-redirect-url="{{ route('login') }}">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </aside>

        <main class="portal-frame-main">
            <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-4 lg:hidden">
                <div>
                    <p class="text-sm font-black text-slate-900">Worker Panel</p>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">CIR</p>
                </div>
                <button class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700" data-drawer-open="worker-sidebar" type="button">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </header>
            <iframe name="contentFrame" src="{{ route('worker.overview') }}" class="portal-frame-embed w-full bg-white" data-worker-frame title="Worker portal content"></iframe>
        </main>
    </div>
@endsection
