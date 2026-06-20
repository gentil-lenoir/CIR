<!DOCTYPE html>
<html lang="en" data-admin-theme="{{ auth()->user()?->theme_preference ?? 'light' }}">
    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <meta content="{{ (auth()->user()?->theme_preference ?? 'light') === 'dark' ? 'dark' : 'light' }}" name="color-scheme" />
        <title>{{ $title ?? 'Admin Dashboard' }}</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="admin-theme min-h-screen bg-slate-100 text-slate-900 overflow-x-hidden" data-admin-theme="{{ auth()->user()?->theme_preference ?? 'light' }}" @if(!empty($openModal)) data-admin-open-modal="{{ $openModal }}" @endif>
        <div class="portal-drawer-backdrop hidden" data-drawer-backdrop="admin-sidebar"></div>
        <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
            <aside class="portal-mobile-sidebar is-closed w-72 text-white flex-shrink-0 flex flex-col min-h-screen z-50 admin-sidebar-shell" id="admin-sidebar">
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
                        <button class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white lg:hidden" data-drawer-close="admin-sidebar" type="button">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                </div>

                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <a class="citizen-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a class="citizen-sidebar-link {{ request()->routeIs('admin.issues.*') ? 'active' : '' }}" href="{{ route('admin.issues.index') }}">
                        <span class="material-symbols-outlined">assignment</span>
                        <span>Issues</span>
                    </a>
                    <a class="citizen-sidebar-link {{ request()->routeIs('admin.workers.*') ? 'active' : '' }}" href="{{ route('admin.workers.index') }}">
                        <span class="material-symbols-outlined">engineering</span>
                        <span>Worker Management</span>
                    </a>
                    <a class="citizen-sidebar-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                        <span class="material-symbols-outlined">domain</span>
                        <span>Departments</span>
                    </a>
                    
                    <div class="pt-4 pb-2 px-4">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Account</span>
                    </div>

                    <a class="citizen-sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.edit') }}">
                        <span class="material-symbols-outlined">person</span>
                        <span>Profile</span>
                    </a>
                    <a class="citizen-sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">
                        <span class="material-symbols-outlined">settings</span>
                        <span>Settings</span>
                    </a>

                    @isset($sidebarAction)
                        <div class="pt-4 pb-2 px-4">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Quick Actions</span>
                        </div>
                        <div class="px-4">
                            <div class="rounded-3xl border border-white/10 bg-white/5 px-4 py-4">
                                {!! $sidebarAction !!}
                            </div>
                        </div>
                    @endisset
                </nav>

                <div class="p-4 border-t border-white/10">
                    <div class="rounded-3xl border border-white/10 bg-white/5 px-4 py-4 mb-4">
                        <p class="text-xs uppercase tracking-[0.28em] text-emerald-300 font-bold">Municipal Manager</p>
                        <p class="mt-3 text-base font-bold text-white">{{ auth()->user()?->name }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ auth()->user()?->email }}</p>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="citizen-sidebar-link w-full text-red-400 hover:text-red-300 hover:bg-red-500/10" type="submit">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex min-h-screen flex-col">
                <header class="border-b border-slate-200 bg-white/95 px-4 py-5 backdrop-blur sm:px-6 lg:px-10">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-start gap-3">
                            <button class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 lg:hidden" data-drawer-open="admin-sidebar" type="button">
                                <span class="material-symbols-outlined">menu</span>
                            </button>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.28em] text-emerald-600">{{ $eyebrow ?? 'Admin Workspace' }}</p>
                                <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">{{ $heading ?? 'Dashboard' }}</h2>
                                @isset($subheading)
                                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{{ $subheading }}</p>
                                @endisset
                            </div>
                        </div>
                        @isset($headerAction)
                            <div class="flex flex-wrap gap-3">{!! $headerAction !!}</div>
                        @endisset
                    </div>
                </header>

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
                    @if (session('success'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                            <p class="font-bold">Please review the form details.</p>
                            <ul class="mt-2 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
