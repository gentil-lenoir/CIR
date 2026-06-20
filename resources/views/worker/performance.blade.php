@extends('layouts.worker-frame')

@section('title', 'Worker Performance - CIR')
@section('worker-page', 'performance')

@section('content')
    <section data-worker-performance>
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary mb-3">Performance</p>
        <h1 class="text-3xl font-extrabold text-slate-900">Operational performance snapshot</h1>
        <p class="mt-3 text-slate-600 max-w-3xl">Measure output, response speed, and service reliability across your current work cycle.</p>
    </section>

    <div class="hidden" data-worker-performance-alert></div>

    <section class="grid gap-6 md:grid-cols-3">
        <article class="worker-chart-container">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Completion Rate</p>
            <h2 class="text-4xl font-extrabold text-slate-900" data-performance-completion-rate>0%</h2>
            <p class="mt-3 text-sm text-slate-500">Resolved compared with your full assignment load.</p>
        </article>
        <article class="worker-chart-container">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Assignments Closed</p>
            <h2 class="text-4xl font-extrabold text-slate-900" data-performance-resolved>0</h2>
            <p class="mt-3 text-sm text-slate-500"><span data-performance-total-assigned>0</span> total assignments handled so far.</p>
        </article>
        <article class="worker-chart-container">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Average Resolution</p>
            <h2 class="text-4xl font-extrabold text-slate-900" data-performance-average-resolution>0h</h2>
            <p class="mt-3 text-sm text-slate-500">Average time from assignment to closure.</p>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <article class="worker-chart-container">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Recent resolved work</p>
            <h2 class="mt-2 text-xl font-extrabold text-slate-900">Latest completed issues</h2>
            <ul class="mt-6 space-y-4" data-performance-recent-resolved>
                <li class="rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500">Loading performance activity...</li>
            </ul>
        </article>

        <article class="worker-chart-container">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Workload signals</p>
            <div class="mt-6 grid gap-4">
                <div class="rounded-3xl bg-slate-50 px-5 py-4">
                    <p class="text-sm font-semibold text-slate-500">Currently Assigned</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900" data-performance-assigned>0</p>
                </div>
                <div class="rounded-3xl bg-slate-50 px-5 py-4">
                    <p class="text-sm font-semibold text-slate-500">In Progress</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900" data-performance-in-progress>0</p>
                </div>
                <div class="rounded-3xl bg-slate-50 px-5 py-4">
                    <p class="text-sm font-semibold text-slate-500">High Priority Open</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900" data-performance-high-priority>0</p>
                </div>
                <div class="rounded-3xl bg-slate-50 px-5 py-4">
                    <p class="text-sm font-semibold text-slate-500">Started Within 24h</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900" data-performance-started-fast>0</p>
                </div>
            </div>
        </article>
    </section>
@endsection
