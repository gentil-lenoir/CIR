<!DOCTYPE html>
<html lang="en" class="light">
    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>@yield('title', 'Worker Portal - CIR')</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
        <script>
            (function () {
                try {
                    const settings = JSON.parse(localStorage.getItem('worker_settings') || '{}');
                    document.documentElement.dataset.workerTheme = settings.theme_preference || 'light';
                } catch (error) {
                    document.documentElement.dataset.workerTheme = 'light';
                }
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="bg-background-light font-display text-slate-800 worker-page-shell"
        data-worker-page="@yield('worker-page')"
        data-api-me-url="{{ url('/api/me') }}"
        data-api-logout-url="{{ url('/api/logout') }}"
        data-api-worker-dashboard-url="{{ url('/api/worker/dashboard') }}"
        data-api-worker-issues-url="{{ url('/api/worker/issues') }}"
        data-api-worker-issue-status-url-template="{{ url('/api/worker/issues/__ISSUE__/status') }}"
        data-api-worker-profile-url="{{ url('/api/worker/profile') }}"
        data-api-worker-settings-url="{{ url('/api/worker/settings') }}"
        data-api-worker-performance-url="{{ url('/api/worker/performance') }}"
        data-dashboard-url="{{ route('worker.dashboard') }}"
        data-login-url="{{ route('login') }}"
    >
        <main class="p-6 md:p-8 lg:p-10 space-y-8">
            @yield('content')
        </main>
    </body>
</html>
