@extends('layouts.worker-frame')

@section('title', 'Assigned Issues - CIR')
@section('worker-page', 'assigned')

@section('content')
    <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary mb-3">Assigned Issues</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Current assignment queue</h1>
            <p class="mt-3 text-slate-600 max-w-3xl">Review all open complaints currently routed to your worker account.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="panel-search-wrap min-w-56">
                <img alt="Search" src="https://api.iconify.design/material-symbols/search-rounded.svg?color=%2364748b" />
                <input class="worker-form-input" data-worker-search placeholder="Search title, address, or category" type="search" />
            </div>
            <div class="dashcode-pill border-blue-200 bg-blue-50 text-blue-700"><span data-worker-count="assigned">0</span> Open Issues</div>
        </div>
    </section>

    <div class="hidden" data-worker-issues-alert></div>

    <section class="worker-chart-container overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="worker-table-header">Issue</th>
                        <th class="worker-table-header">Priority</th>
                        <th class="worker-table-header">Deadline</th>
                        <th class="worker-table-header">Status</th>
                        <th class="worker-table-header">Action</th>
                    </tr>
                </thead>
                <tbody data-worker-assigned-table>
                    <tr>
                        <td class="worker-table-cell text-slate-500" colspan="5">Loading assigned issues...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
