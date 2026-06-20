@extends('layouts.worker-frame')

@section('title', 'In Progress Issues - CIR')
@section('worker-page', 'in-progress')

@section('content')
    <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary mb-3">In Progress</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Active field work tracker</h1>
            <p class="mt-3 text-slate-600 max-w-3xl">Update current work, add progress notes, and upload proof when a complaint is resolved.</p>
        </div>
        <div class="dashcode-pill border-emerald-200 bg-emerald-50 text-emerald-700"><span data-worker-count="in_progress">0</span> Active Jobs</div>
    </section>

    <div class="hidden" data-worker-in-progress-alert></div>

    <section class="grid gap-6 lg:grid-cols-2" data-worker-in-progress-list>
        <article class="worker-task-card p-6">
            <p class="text-sm text-slate-500">Loading active work...</p>
        </article>
    </section>
@endsection
