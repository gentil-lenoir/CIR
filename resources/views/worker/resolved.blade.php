@extends('layouts.worker-frame')

@section('title', 'Resolved Issues - CIR')
@section('worker-page', 'resolved')

@section('content')
    <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary mb-3">Resolved Issues</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Completed work log</h1>
            <p class="mt-3 text-slate-600 max-w-3xl">Review your completed issues with closure timing, proof images, and location details.</p>
        </div>
        <div class="dashcode-pill border-purple-200 bg-purple-50 text-purple-700"><span data-worker-count="resolved">0</span> Resolved</div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2" data-worker-resolved-list>
        <article class="worker-chart-container">
            <p class="text-sm text-slate-500">Loading resolved issues...</p>
        </article>
    </section>
@endsection
