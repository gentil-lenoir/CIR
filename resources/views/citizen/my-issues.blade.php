@extends('layouts.citizen-page')

@section('title', 'My Reported Issues | CIR')
@section('citizen-page', 'issues')
@section('html-class', 'light')
@section('body-class', 'dashcode-page font-display')

@section('content')
    <div class="dashcode-shell max-w-6xl">
        <a href="{{ route('citizen.dashboard') }}" class="dashcode-back-link mb-8">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Back to dashboard
        </a>

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
            <div class="flex items-start gap-4">
                <div class="dashcode-icon-chip">
                    <span class="material-symbols-outlined text-2xl">list_alt</span>
                </div>
                <div>
                    <h1 class="dashcode-section-title">My Reported Issues</h1>
                    <p class="dashcode-muted mt-2">Track resolution progress, assignment details, and status changes for every issue you submitted.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <span class="dashcode-pill bg-blue-50 text-blue-700 border-blue-200"><span data-issues-total>0</span> Total</span>
            </div>
        </div>

        <div class="dashcode-surface p-5 mb-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="panel-search-wrap">
                <img alt="Search" src="https://api.iconify.design/material-symbols/search-rounded.svg?color=%2364748b" />
                <input class="dashcode-input" data-issues-search placeholder="Search your issues by title, location, or category" type="text" />
            </div>
            <select class="dashcode-select" data-issues-status-filter>
                <option value="">All statuses</option>
                <option value="reported">Reported</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
            </select>
        </div>

        <div class="space-y-5" data-issues-list>
            <div class="dashcode-surface p-6 text-sm text-navy-500">Loading your issues...</div>
        </div>
    </div>
@endsection
