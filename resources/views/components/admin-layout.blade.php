@props([
    'title' => 'Admin Dashboard',
    'eyebrow' => null,
    'heading' => null,
    'subheading' => null,
    'headerAction' => null,
    'sidebarAction' => null,
    'openModal' => null,
])

@include('admin.layout', [
    'title' => $title,
    'eyebrow' => $eyebrow,
    'heading' => $heading,
    'subheading' => $subheading,
    'headerAction' => $headerAction,
    'sidebarAction' => $sidebarAction,
    'openModal' => $openModal,
    'slot' => $slot,
])
