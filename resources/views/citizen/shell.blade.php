@extends('layouts.citizen-page')

@section('title', 'Citizen Dashboard - CIR')
@section('citizen-page', 'shell')

@section('html-class', 'light')

@section('body-class', 'dashcode-page font-display portal-frame-shell')

@section('content')
    <div class="portal-drawer-backdrop hidden" data-drawer-backdrop="citizen-sidebar"></div>
    <div class="min-h-screen lg:flex">
        <aside class="portal-mobile-sidebar is-closed w-72 bg-navy-950 text-white flex-shrink-0 flex flex-col h-screen z-50" id="citizen-sidebar">
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
                    <button class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white lg:hidden" data-drawer-close="citizen-sidebar" type="button">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <a
                class="citizen-sidebar-link active"
                href="{{ route('citizen.dashcode') }}"
                target="contentFrame"
                data-citizen-nav
            >
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium">Dashboard Overview</span>
            </a>
            <a class="citizen-sidebar-link" href="{{ route('citizen.report') }}" target="contentFrame" data-citizen-nav>
                <span class="material-symbols-outlined">add_circle</span>
                <span class="font-medium">Report New Issue</span>
            </a>

            <a class="citizen-sidebar-link" href="{{ route('citizen.my_issues') }}" target="contentFrame" data-citizen-nav>
                <span class="material-symbols-outlined">list_alt</span>
                <span class="font-medium">My Reported Issues</span>
            </a>            
            
            <a class="citizen-sidebar-link" href="/" target="_self" data-citizen-nav>
                <span class="material-symbols-outlined">home</span>
                <span class="font-medium">Home Page</span>
            </a>

            <div class="pt-4 pb-2 px-4">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Account</span>
            </div>
            <a class="citizen-sidebar-link" href="{{ route('citizen.profile') }}" target="contentFrame" data-citizen-nav>
                <span class="material-symbols-outlined">account_circle</span>
                <span class="font-medium">My Profile</span>
            </a>

            </nav>
            <div class="p-4 border-t border-white/10">
                <div class="rounded-3xl border border-white/10 bg-white/5 px-4 py-4 mb-4">
                    <p class="text-xs uppercase tracking-[0.28em] text-emerald-300 font-bold">Citizen</p>
                    <p class="mt-3 text-base font-bold text-white" data-citizen-shell-name>Citizen</p>
                    <p class="mt-1 text-sm text-slate-400" data-citizen-shell-email>citizen@example.com</p>
                </div>
                <a
                    class="citizen-sidebar-link text-red-400 hover:text-red-300 hover:bg-red-500/10"
                    href="{{ route('login') }}"
                    data-auth-logout
                    data-redirect-url="{{ route('login') }}"
                >
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </aside>

        <main class="portal-frame-main">
            <header class="flex items-center justify-between border-b border-navy-200 bg-white px-4 py-4 lg:hidden">
                <div>
                    <p class="text-sm font-black text-navy-900">Citizen Panel</p>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-navy-500">CIR</p>
                </div>
                <button class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-navy-200 bg-white text-navy-700" data-drawer-open="citizen-sidebar" type="button">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </header>
            <iframe
                name="contentFrame"
                src="{{ route('citizen.dashcode') }}"
                class="portal-frame-embed w-full bg-navy-100"
                title="Citizen portal content"
                data-citizen-frame
            ></iframe>
        </main>
    </div>

    <script>
        document.querySelectorAll('a[data-citizen-nav]').forEach(function (link) {
            link.addEventListener('click', function () {
                document.querySelectorAll('a[data-citizen-nav]').forEach(function (a) {
                    a.classList.remove('active');
                });
                link.classList.add('active');
                document.getElementById('citizen-sidebar')?.classList.add('is-closed');
                document.querySelector('[data-drawer-backdrop="citizen-sidebar"]')?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        });
    </script>
@endsection
