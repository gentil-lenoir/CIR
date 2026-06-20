<!DOCTYPE html>
<html class="light" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>@yield('title', 'CIR - Smart Public Issue Reporting & Resolution')</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('page_assets')
    </head>

    <body class="dashcode-page font-display">
        @include('components.navbar')

        @yield('content')

        @include('components.footer')
    </body>
</html>

