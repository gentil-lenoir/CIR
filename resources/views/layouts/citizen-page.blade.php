<!DOCTYPE html>
<html lang="en" class="@yield('html-class')">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>@yield('title', 'CIR')</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
        <script>
            (function () {
                try {
                    const settings = JSON.parse(localStorage.getItem('citizen_settings') || '{}');
                    document.documentElement.dataset.citizenTheme = settings.theme_preference || 'light';
                } catch (error) {
                    document.documentElement.dataset.citizenTheme = 'light';
                }
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth.js'])
        @stack('head')
    </head>
    <body
        class="@yield('body-class')"
        data-citizen-page="@yield('citizen-page')"
        data-api-logout-url="{{ url('/api/logout') }}"
        data-api-me-url="{{ url('/api/me') }}"
        data-api-citizen-dashboard-url="{{ url('/api/citizen/dashboard') }}"
        data-api-citizen-issues-url="{{ url('/api/citizen/issues') }}"
        data-api-citizen-profile-url="{{ url('/api/citizen/profile') }}"
        data-api-citizen-settings-url="{{ url('/api/citizen/settings') }}"
        data-api-create-issue-url="{{ url('/api/issues') }}"
        data-api-issues-url="{{ url('/api/issues') }}"
        data-dashboard-url="{{ route('citizen.dashboard') }}"
        data-login-url="{{ route('login') }}"
    >
        @yield('content')
    </body>
</html>
