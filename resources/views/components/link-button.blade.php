@props(['variant' => 'primary'])

@php
    $classes = [
        'primary' => 'bg-gray-900 text-white hover:bg-gray-700 focus:ring-gray-900 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white dark:focus:ring-gray-100',
        'secondary' => 'border border-gray-300 bg-white text-gray-800 hover:bg-gray-50 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700',
        'subtle' => 'text-gray-700 hover:text-gray-950 focus:ring-gray-500 dark:text-gray-300 dark:hover:text-white',
        'danger' => 'bg-red-600 text-white hover:bg-red-500 focus:ring-red-500',
    ][$variant] ?? 'bg-gray-900 text-white hover:bg-gray-700 focus:ring-gray-900';
@endphp

<a {{ $attributes->merge(['class' => "inline-flex min-h-10 items-center justify-center rounded-md px-4 py-2 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900 {$classes}"]) }}>
    {{ $slot }}
</a>
