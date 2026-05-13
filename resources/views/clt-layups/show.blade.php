<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-breadcrumbs :items="[
                    ['label' => __('Suppliers'), 'url' => route('suppliers.index')],
                    ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
                    ['label' => __('Layups'), 'url' => route('suppliers.layups.index', $supplier)],
                    ['label' => $layup->name],
                ]" />
                <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                    {{ $layup->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Layer stack for this layup.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-link-button variant="secondary" href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}">
                    {{ __('Edit Layup') }}
                </x-link-button>
                <x-link-button href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}">
                    {{ __('Add Layer') }}
                </x-link-button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            <div class="mb-6 grid gap-4 md:grid-cols-3">
                <x-metric :label="__('Layers')" :value="$layup->layers->count()" tone="emerald" />
                <x-metric :label="__('First layer')" :value="$layup->layers->first()?->layer_order ?? '-'" tone="sky" />
                <x-metric :label="__('Last layer')" :value="$layup->layers->last()?->layer_order ?? '-'" tone="amber" />
            </div>

            <x-panel class="overflow-hidden">
                <div class="flex flex-col gap-3 border-b border-gray-200 p-6 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Layers') }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Layers are ordered from the outside of the stack inward.') }}</p>
                    </div>
                    <x-link-button variant="secondary" href="{{ route('suppliers.layups.layers.index', [$supplier, $layup]) }}">
                        {{ __('Manage Layer Table') }}
                    </x-link-button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-900/60 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3 font-medium">{{ __('Order') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Thickness') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Width') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Angle') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($layup->layers as $layer)
                                <tr class="text-gray-900 dark:text-gray-100">
                                    <td class="px-6 py-4 font-semibold">{{ $layer->layer_order }}</td>
                                    <td class="px-6 py-4">{{ $layer->thickness }}</td>
                                    <td class="px-6 py-4">{{ $layer->width }}</td>
                                    <td class="px-6 py-4">{{ $layer->angle }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8">
                                        <x-empty-state
                                            :title="__('No layers yet')"
                                            :body="__('Add the first layer to define this layup stack.')"
                                            :action-label="__('Add Layer')"
                                            :action-url="route('suppliers.layups.layers.create', [$supplier, $layup])"
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-panel>
        </div>
    </div>
</x-app-layout>
