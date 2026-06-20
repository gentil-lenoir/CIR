<nav class="dashcode-topbar">
    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
        <div class="flex items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-2xl bg-linear-to-b from-[#21d4b4] to-primary text-white shadow-[0_18px_36px_rgba(16,185,129,0.28)]">
                    <span class="material-symbols-outlined text-[30px] leading-none">location_city</span>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-[1.35rem] font-black leading-none tracking-[-0.05em] text-white sm:text-[1.65rem]"><span class="text-primary">CIR</span></p>
                    <p class="mt-1 truncate text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-300">Smart Public Issue Reporting</p>
                </div>
            </div>

            <div class="hidden md:flex md:flex-1 md:justify-center">
                <div class="landing-nav-pill">
                    <a class="nav-link" href="#home">Home</a>
                    <a class="nav-link" href="#about">About</a>
                    <a class="nav-link" href="#users">Users</a>
                    <a class="nav-link" href="#features">Features</a>
                </div>
            </div>

            <div class="hidden items-center gap-3 md:flex"
                data-login-url="{{ route('login') }}"
                data-register-url="{{ route('register') }}">
                <a class="landing-auth-link landing-auth-link-secondary auth-guest-link" href="{{ route('login') }}">Login</a>
                <a class="landing-auth-link landing-auth-link-primary auth-guest-link" href="{{ route('register') }}">Sign Up</a>
            </div>

            <button class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white md:hidden" data-mobile-menu-toggle="landing-mobile-menu" type="button">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>

        <div class="landing-mobile-menu hidden pt-4" id="landing-mobile-menu">
            <div class="space-y-3 rounded-3xl border border-white/10 bg-white/5 p-4">
                <div class="grid grid-cols-2 gap-2">
                    <a class="nav-link justify-start" href="#home">Home</a>
                    <a class="nav-link justify-start" href="#about">About</a>
                    <a class="nav-link justify-start" href="#users">Users</a>
                    <a class="nav-link justify-start" href="#features">Features</a>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a class="landing-auth-link landing-auth-link-secondary auth-guest-link flex-1" href="{{ route('login') }}">Login</a>
                    <a class="landing-auth-link landing-auth-link-primary auth-guest-link flex-1" href="{{ route('register') }}">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</nav>
