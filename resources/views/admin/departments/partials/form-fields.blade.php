@php
    $isEditing = filled($department?->id);
@endphp

<div>
    <label class="admin-form-label" for="department-name-{{ $isEditing ? $department->id : 'create' }}">Department Name</label>
    <input
        class="admin-form-input"
        id="department-name-{{ $isEditing ? $department->id : 'create' }}"
        name="name"
        placeholder="Public Works"
        required
        type="text"
        value="{{ old('name', $department?->name) }}"
    />
    <p class="mt-2 text-xs text-slate-500">Choose a clear department name the admin team and workers will recognize immediately.</p>
</div>

<div>
    <label class="admin-form-label" for="department-description-{{ $isEditing ? $department->id : 'create' }}">Description</label>
    <textarea
        class="admin-form-input min-h-36"
        id="department-description-{{ $isEditing ? $department->id : 'create' }}"
        name="description"
        placeholder="Handles roads, drains, street repairs, and other public infrastructure requests."
    >{{ old('description', $department?->description) }}</textarea>
    <p class="mt-2 text-xs text-slate-500">Optional, but helpful for explaining what types of issues should be routed here.</p>
</div>
