@php
    $isEditing = filled($worker);
@endphp

<div class="md:col-span-2">
    <label class="admin-form-label" for="worker-name-{{ $isEditing ? $worker->id : 'create' }}">Full Name</label>
    <input class="admin-form-input" id="worker-name-{{ $isEditing ? $worker->id : 'create' }}" name="name" required type="text" value="{{ old('name', $worker?->name) }}" />
</div>
<div>
    <label class="admin-form-label" for="worker-email-{{ $isEditing ? $worker->id : 'create' }}">Email Address</label>
    <input class="admin-form-input" id="worker-email-{{ $isEditing ? $worker->id : 'create' }}" name="email" required type="email" value="{{ old('email', $worker?->email) }}" />
</div>
<div>
    <label class="admin-form-label" for="worker-phone-{{ $isEditing ? $worker->id : 'create' }}">Phone Number</label>
    <input class="admin-form-input" id="worker-phone-{{ $isEditing ? $worker->id : 'create' }}" name="phone" type="text" value="{{ old('phone', $worker?->phone) }}" />
</div>
<div>
    <label class="admin-form-label" for="worker-department-{{ $isEditing ? $worker->id : 'create' }}">Department</label>
    <select class="admin-form-input" id="worker-department-{{ $isEditing ? $worker->id : 'create' }}" name="department_id" required>
        @foreach ($departments as $department)
            <option @selected((int) old('department_id', $worker?->department_id) === $department->id) value="{{ $department->id }}">{{ $department->name }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="admin-form-label" for="worker-status-{{ $isEditing ? $worker->id : 'create' }}">Account Status</label>
    <select class="admin-form-input" id="worker-status-{{ $isEditing ? $worker->id : 'create' }}" name="status" required>
        <option @selected(old('status', $worker?->status) === 'active') value="active">Active</option>
        <option @selected(old('status', $worker?->status) === 'inactive') value="inactive">Inactive</option>
    </select>
</div>
<div>
    <label class="admin-form-label" for="worker-availability-{{ $isEditing ? $worker->id : 'create' }}">Availability</label>
    <select class="admin-form-input" id="worker-availability-{{ $isEditing ? $worker->id : 'create' }}" name="availability_status" required>
        <option @selected(old('availability_status', $worker?->availability_status ?? 'available') === 'available') value="available">Available</option>
        <option @selected(old('availability_status', $worker?->availability_status) === 'busy') value="busy">Busy</option>
        <option @selected(old('availability_status', $worker?->availability_status) === 'offline') value="offline">Offline</option>
    </select>
</div>
<div>
    <label class="admin-form-label" for="worker-theme-{{ $isEditing ? $worker->id : 'create' }}">Theme Preference</label>
    <select class="admin-form-input" id="worker-theme-{{ $isEditing ? $worker->id : 'create' }}" name="theme_preference" required>
        <option @selected(old('theme_preference', $worker?->theme_preference ?? 'light') === 'light') value="light">Light</option>
        <option @selected(old('theme_preference', $worker?->theme_preference) === 'dark') value="dark">Dark</option>
    </select>
</div>
<div>
    <label class="admin-form-label" for="worker-zone-{{ $isEditing ? $worker->id : 'create' }}">Preferred Zone</label>
    <input class="admin-form-input" id="worker-zone-{{ $isEditing ? $worker->id : 'create' }}" name="preferred_zone" type="text" value="{{ old('preferred_zone', $worker?->preferred_zone) }}" />
</div>
<div>
    <label class="admin-form-label" for="worker-shift-{{ $isEditing ? $worker->id : 'create' }}">Shift Window</label>
    <input class="admin-form-input" id="worker-shift-{{ $isEditing ? $worker->id : 'create' }}" name="shift_window" placeholder="08:00 AM - 05:00 PM" type="text" value="{{ old('shift_window', $worker?->shift_window) }}" />
</div>
<div class="md:col-span-2">
    <label class="admin-form-label" for="worker-password-{{ $isEditing ? $worker->id : 'create' }}">Password</label>
    <input class="admin-form-input" id="worker-password-{{ $isEditing ? $worker->id : 'create' }}" name="password" {{ $isEditing ? '' : 'required' }} placeholder="{{ $isEditing ? 'Leave blank to keep current password' : 'Minimum 8 characters' }}" type="password" />
</div>
@unless ($isEditing)
    <div class="md:col-span-2">
        <label class="admin-form-label" for="worker-password-confirmation-create">Confirm Password</label>
        <input class="admin-form-input" id="worker-password-confirmation-create" name="password_confirmation" required placeholder="Repeat the worker password" type="password" />
    </div>
@endunless
