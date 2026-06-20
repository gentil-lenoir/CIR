@extends('layouts.citizen-page')

@section('title', 'Citizen Dashboard - CIR')
@section('citizen-page', 'dashboard')
@section('html-class', 'light')
@section('body-class', 'dashcode-page font-sans')

@push('head')
    <style>
        h1,
        h2,
        h3,
        h4 {
            font-family: 'Poppins', sans-serif;
        }
    </style>
@endpush

@section('content')
    <header class="bg-white border-b border-navy-200 sticky top-0 z-50">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-[52px] w-[52px] items-center justify-center rounded-2xl bg-linear-to-b from-[#21d4b4] to-primary text-white shadow-[0_18px_36px_rgba(16,185,129,0.28)]">
                    <span class="material-symbols-outlined text-[30px] leading-none">location_city</span>
                </div>
                <div>
                    <p class="text-[1.8rem] font-black leading-none tracking-[-0.05em] text-navy-900"><span class="text-primary">CIR</span></p>
                    <p class="mt-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-navy-500">Smart Public Issue Reporting</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('citizen.profile') }}" class="flex items-center gap-3 pl-2 pr-1 py-1 hover:bg-navy-50 rounded-2xl transition-all border border-transparent hover:border-navy-100">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-navy-900 leading-none" data-citizen-name>Citizen</p>
                        <p class="text-[12px] text-navy-500 font-medium mt-1">Verified Citizen</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">CF</div>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-[1400px] mx-auto px-6 lg:px-12 py-10 text-navy-800 antialiased">
        <section class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-navy-900 tracking-tight lg:text-4xl">Welcome, <span data-citizen-name>Citizen</span></h1>
                <p class="text-navy-500 mt-2 text-lg font-medium">Track your complaints, report new issues, and monitor how the city responds.</p>
            </div>
            <div class="inline-flex items-center gap-3 text-sm font-bold text-navy-700 bg-white px-5 py-3 rounded-2xl citizen-stat-card-shadow border border-white">
                <div class="bg-emerald-50 p-1.5 rounded-lg">
                    <img alt="Location" class="h-5 w-5" src="https://api.iconify.design/material-symbols/location-on-rounded.svg?color=%2310b981" />
                </div>
                <span data-stat="preferred_location">No preferred location set yet</span>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-6 rounded-3xl border border-white citizen-stat-card-shadow transition-all hover:shadow-lg">
                <p class="text-xs font-bold text-navy-400 uppercase tracking-widest">Total Reported</p>
                <h3 class="mt-4 text-4xl font-extrabold text-navy-900" data-stat="total_reported">0</h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-white citizen-stat-card-shadow transition-all hover:shadow-lg">
                <p class="text-xs font-bold text-navy-400 uppercase tracking-widest">In Progress</p>
                <h3 class="mt-4 text-4xl font-extrabold text-navy-900" data-stat="in_progress">0</h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-white citizen-stat-card-shadow transition-all hover:shadow-lg">
                <p class="text-xs font-bold text-navy-400 uppercase tracking-widest">Resolved</p>
                <h3 class="mt-4 text-4xl font-extrabold text-navy-900" data-stat="resolved">0</h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-white citizen-stat-card-shadow transition-all hover:shadow-lg">
                <p class="text-xs font-bold text-navy-400 uppercase tracking-widest">Upvotes Received</p>
                <h3 class="mt-4 text-4xl font-extrabold text-navy-900" data-stat="upvotes_received">0</h3>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <a href="{{ route('citizen.report') }}" class="citizen-action-tile group bg-white p-8 rounded-[32px] border border-white citizen-stat-card-shadow flex flex-col items-center text-center">
                <div class="relative w-20 h-20 bg-emerald-50 rounded-[24px] flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <img alt="Report issue" class="h-11 w-11 transition-opacity group-hover:opacity-0 absolute" src="https://api.iconify.design/material-symbols/add-circle-rounded.svg?color=%2310b981" />
                    <img alt="" aria-hidden="true" class="h-11 w-11 opacity-0 transition-opacity group-hover:opacity-100" src="https://api.iconify.design/material-symbols/add-circle-rounded.svg?color=%23ffffff" />
                </div>
                <h4 class="text-xl font-bold text-navy-900 mb-2">Report New Issue</h4>
                <p class="text-navy-500 text-sm leading-relaxed max-w-[240px]">Pin the location, upload photos, and submit a real complaint to the live system.</p>
            </a>

            <a href="{{ route('citizen.my_issues') }}" class="citizen-action-tile group bg-white p-8 rounded-[32px] border border-white citizen-stat-card-shadow flex flex-col items-center text-center">
                <div class="relative w-20 h-20 bg-purple-50 rounded-[24px] flex items-center justify-center mb-6 group-hover:bg-purple-600 transition-colors">
                    <img alt="History" class="h-11 w-11 transition-opacity group-hover:opacity-0 absolute" src="https://api.iconify.design/material-symbols/history-edu-rounded.svg?color=%239333ea" />
                    <img alt="" aria-hidden="true" class="h-11 w-11 opacity-0 transition-opacity group-hover:opacity-100" src="https://api.iconify.design/material-symbols/history-edu-rounded.svg?color=%23ffffff" />
                </div>
                <h4 class="text-xl font-bold text-navy-900 mb-2">My Reported Issues</h4>
                <p class="text-navy-500 text-sm leading-relaxed max-w-[240px]">See your real complaint history, assigned workers, and updates.</p>
            </a>
        </section>

        <section>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 bg-primary rounded-full"></div>
                    <h3 class="text-xl font-bold text-navy-900 uppercase tracking-tight">Recent Updates</h3>
                </div>
                <a href="{{ route('citizen.my_issues') }}" class="text-sm font-bold text-primary hover:text-emerald-700 transition-colors flex items-center gap-1 group">
                    View All Reports
                    <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
            <div class="bg-white rounded-3xl border border-white citizen-stat-card-shadow overflow-hidden">
                <div class="divide-y divide-navy-100" data-recent-issues>
                    <div class="p-6 text-sm text-navy-500">Loading your latest reports...</div>
                </div>
            </div>
        </section>
    </main>
@endsection
