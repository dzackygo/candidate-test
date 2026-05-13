@props(['type' => 'success'])

@php
    $classes = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-100',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100',
        'info' => 'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-900 dark:bg-sky-950 dark:text-sky-100',
    ][$type] ?? 'border-gray-200 bg-gray-50 text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100';
@endphp

<div {{ $attributes->merge(['class' => "rounded-md border px-4 py-3 text-sm {$classes}"]) }}>
    {{ $slot }}
</div>
