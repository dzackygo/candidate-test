@props(['title', 'body', 'actionLabel' => null, 'actionUrl' => null])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center dark:border-gray-700 dark:bg-gray-900/40']) }}>
    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-xl text-sm text-gray-600 dark:text-gray-400">{{ $body }}</p>

    @if ($actionLabel && $actionUrl)
        <div class="mt-5">
            <x-link-button :href="$actionUrl">{{ $actionLabel }}</x-link-button>
        </div>
    @endif
</div>
