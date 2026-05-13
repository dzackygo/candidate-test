@props(['label', 'value', 'tone' => 'gray'])

@php
    $classes = [
        'gray' => 'border-gray-200 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100',
        'sky' => 'border-sky-200 bg-sky-50 text-sky-950 dark:border-sky-900 dark:bg-sky-950 dark:text-sky-100',
        'emerald' => 'border-emerald-200 bg-emerald-50 text-emerald-950 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-100',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100',
    ][$tone] ?? 'border-gray-200 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100';
@endphp

<div {{ $attributes->merge(['class' => "rounded-lg border p-4 {$classes}"]) }}>
    <div class="text-2xl font-semibold leading-none">{{ $value }}</div>
    <div class="mt-1 text-sm opacity-75">{{ $label }}</div>
</div>
