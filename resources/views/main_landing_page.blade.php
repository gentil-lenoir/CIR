@extends('layouts.app')

@push('page_assets')
    @vite(['resources/css/style.css', 'resources/js/main.js'])
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="bg-white py-16 md:py-24 px-6 md:px-12 lg:px-24" id="home">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 md:gap-16 items-center">
            <!-- Left Content -->
            <div class="text-center lg:text-left">
                <span class="inline-block px-4 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-blue-600 text-xs font-semibold uppercase tracking-wider mb-6">
                    Smarter Cities, Better Lives
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                    Report. Track. 
                    <span class="text-blue-600">Resolve.</span>
                </h1>
                <p class="text-lg text-gray-600 mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    CIR turns citizen reports into visible action with real-time issue tracking, location-based reporting, and a resolution pipeline built for municipal response.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-4 rounded-lg text-lg transition-all shadow-sm flex items-center justify-center gap-2 min-h-[56px]"
                        id="report-issue-btn"
                        data-login-url="{{ route('login') }}"
                        type="button">
                        <span>Report Issue</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Right Card -->
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm">
                <div class="space-y-4">
                    <div class="bg-white border border-gray-100 rounded-xl p-6">
                        <p class="text-gray-500 text-sm font-medium uppercase tracking-wide mb-2">Live System Snapshot</p>
                        <p class="text-2xl font-bold text-gray-900">City Response Hub</p>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center">
                            <span class="text-xs text-gray-500 font-medium block mb-1">Total</span>
                            <span class="text-2xl font-bold text-gray-900" data-stat-value="total">--</span>
                        </div>
                        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center">
                            <span class="text-xs text-gray-500 font-medium block mb-1">Resolved</span>
                            <span class="text-2xl font-bold text-green-600" data-stat-value="resolved">--</span>
                        </div>
                        <div class="bg-white border border-gray-100 rounded-xl p-4 text-center">
                            <span class="text-xs text-gray-500 font-medium block mb-1">Pending</span>
                            <span class="text-2xl font-bold text-orange-500" data-stat-value="pending">--</span>
                        </div>
                    </div>

                    <div class="bg-gray-100 rounded-xl p-5 flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-700">Location-tagged issue reporting connected to field operations and transparent updates.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="bg-gray-50 py-16 md:py-20 px-6 md:px-12 lg:px-24" id="about">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-[1.2fr_0.8fr] gap-10 items-start">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 mb-3">About Section</p>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">A smarter way to connect public issues with real action</h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-8">
                    CIR gives citizens a direct channel to report problems, helps departments prioritize urgent work, and keeps the full resolution journey visible from submission to completion.
                </p>
                
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Visual Reporting</h3>
                        <p class="text-gray-600 text-sm">Attach photos, capture context, and make each complaint actionable from the start.</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Smart Routing</h3>
                        <p class="text-gray-600 text-sm">Direct issues to the right municipal team with less delay and better accountability.</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Transparent Updates</h3>
                        <p class="text-gray-600 text-sm">Citizens and workers stay aligned with visible status changes and progress tracking.</p>
                    </div>
                </div>
            </div>

            <!-- Stats Panel -->
            <aside class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm"
                data-stats-url="{{ url('/api/stats') }}"
                data-login-url="{{ route('login') }}">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 mb-2">System Stats</p>
                        <h3 class="text-xl font-bold text-gray-900">Real-time civic response at a glance</h3>
                    </div>
                    <svg class="w-8 h-8 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600">Total Issues</span>
                        <strong class="text-lg text-gray-900" id="total-issues-value">--</strong>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600">Resolved Issues</span>
                        <strong class="text-lg text-green-600" id="resolved-issues-value">--</strong>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600">Pending Issues</span>
                        <strong class="text-lg text-orange-500" id="pending-issues-value">--</strong>
                    </div>
                </div>
                
                <p class="hidden mt-6 text-sm text-gray-500" id="stats-feedback" role="status"></p>
            </aside>
        </div>
    </section>

    <!-- User Types Section -->
    <section class="bg-white py-16 md:py-20 px-6 md:px-12 lg:px-24" id="users">
        <div class="max-w-7xl mx-auto">
            <div class="max-w-3xl mb-10">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 mb-3">User Types</p>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Built for every stakeholder in the civic response cycle</h2>
                <p class="text-gray-600 text-lg">
                    The platform gives each user a focused workspace while keeping issue reporting, assignment, and resolution connected in one flow.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Citizen Card -->
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">Citizen</p>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Report local issues and monitor progress</h3>
                    <p class="text-gray-600 mb-4">Citizens can submit complaints with location details, track status changes, and stay informed until resolution.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Submit issues with photos and descriptions
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            See updates from municipal teams
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Track personal issue history
                        </li>
                    </ul>
                </div>

                <!-- Worker Card -->
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-2">Worker</p>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Handle field assignments with clarity</h3>
                    <p class="text-gray-600 mb-4">Workers receive assigned issues, manage active work, and close cases with visible progress updates.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            View assigned issue queues
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Update issue progress and status
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Manage operational workload efficiently
                        </li>
                    </ul>
                </div>

                <!-- Admin Card -->
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide mb-2">Admin</p>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Coordinate city-wide response and oversight</h3>
                    <p class="text-gray-600 mb-4">Administrators monitor system activity, assign work to departments and workers, and analyze resolution performance.</p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Assign issues to responsible teams
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Monitor dashboards and response metrics
                        </li>
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Maintain accountability across departments
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-gray-50 py-16 md:py-20 px-6 md:px-12 lg:px-24" id="features">
        <div class="max-w-7xl mx-auto">
            <div class="max-w-3xl mb-10">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 mb-3">Platform Features</p>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Core features that make the system practical and accountable</h2>
                <p class="text-gray-600 text-lg">
                    The landing page now highlights the actual strengths of the application instead of only describing it in text.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Location-Based Reporting</h3>
                    <p class="text-gray-600 text-sm">Capture issue location and context so city teams can respond precisely.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Status Tracking</h3>
                    <p class="text-gray-600 text-sm">Track every issue from reported to assigned, in progress, and resolved.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Role-Based Workspaces</h3>
                    <p class="text-gray-600 text-sm">Citizens, workers, and admins each get interfaces tailored to their responsibilities.</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Operational Visibility</h3>
                    <p class="text-gray-600 text-sm">Live stats, assignment views, and progress updates improve municipal accountability.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-blue-600 py-16 md:py-20 px-6 md:px-12 lg:px-24" id="cta">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6">
                Ready to make a visible difference?
            </h2>
            <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">
                Move from static information to live civic action with one place to report, monitor, and improve public spaces.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button
                    class="bg-white text-blue-600 font-semibold px-8 py-4 rounded-lg text-lg hover:bg-gray-100 transition-all shadow-sm inline-flex items-center justify-center gap-2 min-h-[56px]"
                    id="get-started-btn"
                    data-login-url="{{ route('login') }}"
                    type="button">
                    Get Started Now
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>
@endsection