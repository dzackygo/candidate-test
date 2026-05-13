@props(['items' => []])

@if (count($items) > 0)
    <nav {{ $attributes->merge(['class' => 'mb-3 flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400']) }} aria-label="Breadcrumb">
        @foreach ($items as $item)
            @if (! $loop->first)
                <span class="text-gray-300 dark:text-gray-600">/</span>
            @endif

            @if (! empty($item['url']) && ! $loop->last)
                <a href="{{ $item['url'] }}" class="font-medium hover:text-gray-900 dark:hover:text-gray-100">
                    {{ $item['label'] }}
                </a>
            @else
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $item['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif
