@extends('layouts.citizen-page')

@section('title', 'Report New Issue | CIR')
@section('citizen-page', 'report')
@section('html-class', 'light')
@section('body-class', 'dashcode-page font-display')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
    <div class="dashcode-shell max-w-6xl">
        <a href="{{ route('citizen.dashboard') }}" class="dashcode-back-link mb-8">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Back to dashboard
        </a>

        <div class="dashcode-panel p-6 sm:p-8 lg:p-10">
            <div class="flex items-start gap-4 mb-10">
                <div class="dashcode-icon-chip w-14 h-14 rounded-3xl">
                    <span class="material-symbols-outlined text-3xl">add_circle</span>
                </div>
                <div>
                    <h1 class="dashcode-section-title">Report New Issue</h1>
                    <p class="dashcode-muted mt-2 max-w-2xl">Describe the issue, pin its location on the map, and upload images to help the city respond faster.</p>
                </div>
            </div>

            <div class="hidden mb-6" data-report-message></div>

            <form class="space-y-8" data-report-form>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="dashcode-label">Issue Title</label>
                        <input class="dashcode-input" name="title" placeholder="e.g. Pothole on Main Street near bus stop" required type="text" />
                    </div>
                    <div>
                        <label class="dashcode-label">Category</label>
                        <select class="dashcode-select" name="category">
                            <option value="roads">Roads & Potholes</option>
                            <option value="lighting">Lighting</option>
                            <option value="water">Water & Drainage</option>
                            <option value="sanitation">Sanitation</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="dashcode-label">Address / Landmark</label>
                        <input class="dashcode-input" name="address" placeholder="Enter area, landmark, or street name" type="text" />
                    </div>
                    <div>
                        <label class="dashcode-label">Priority</label>
                        <select class="dashcode-select" name="priority">
                            <option value="medium">Needs attention soon</option>
                            <option value="low">Normal issue</option>
                            <option value="high">High concern</option>
                            <option value="urgent">Urgent safety concern</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="dashcode-label">Description</label>
                    <textarea class="dashcode-textarea" name="description" placeholder="Share what you observed, when it started, and what impact it is having." required rows="6"></textarea>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-6">
                    <div class="dashcode-surface p-6 space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-navy-900">Pin Exact Location</h3>
                                <p class="text-sm text-navy-500">Click on the map or use your current location.</p>
                            </div>
                            <button class="dashcode-btn-secondary" data-use-location type="button">Use Current Location</button>
                        </div>
                        <div class="h-80 rounded-[24px] border border-navy-200 overflow-hidden" data-report-map></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input class="dashcode-input" name="latitude" placeholder="Latitude" readonly required type="text" />
                            <input class="dashcode-input" name="longitude" placeholder="Longitude" readonly required type="text" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="dashcode-surface p-6 border-dashed border-navy-200">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl">upload</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-navy-900">Upload Images</h3>
                                    <p class="text-sm text-navy-500">Attach supporting photos to speed up verification.</p>
                                </div>
                            </div>
                            <div class="mt-6 rounded-[24px] bg-navy-50 border border-navy-200 px-6 py-10 text-center">
                                <span class="material-symbols-outlined text-5xl text-navy-300 block mb-3">image</span>
                                <label class="font-semibold text-navy-700 cursor-pointer block">
                                    Browse image files
                                    <input accept=".png,.jpg,.jpeg" class="hidden" multiple name="images[]" type="file" />
                                </label>
                                <p class="text-sm text-navy-400 mt-1">PNG and JPG up to 5 MB each</p>
                                <div class="mt-4 space-y-3" data-upload-preview>
                                    <p class="text-sm text-navy-400">No files selected yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <button class="dashcode-btn-secondary flex-1" type="reset">Reset Form</button>
                    <button class="dashcode-btn-primary flex-1" type="submit">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
@endsection
