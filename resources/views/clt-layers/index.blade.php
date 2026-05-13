<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-breadcrumbs :items="[
                    ['label' => __('Suppliers'), 'url' => route('suppliers.index')],
                    ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
                    ['label' => __('Layups'), 'url' => route('suppliers.layups.index', $supplier)],
                    ['label' => $layup->name, 'url' => route('suppliers.layups.show', [$supplier, $layup])],
                    ['label' => __('Layers')],
                ]" />
                <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                    {{ __('Layers') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Edit the ordered layer stack for this layup.') }}</p>
            </div>
            <x-link-button href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}">
                {{ __('Add Layer') }}
            </x-link-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            <x-panel class="overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900/60">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $layup->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $layers->total() }} {{ __('layers total') }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-white text-left text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3 font-medium">{{ __('Order') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Thickness') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Width') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Angle') }}</th>
                                <th class="px-6 py-3 text-right font-medium">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($layers as $layer)
                                <tr class="text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-gray-900/40">
                                    <td class="px-6 py-4 font-semibold">{{ $layer->layer_order }}</td>
                                    <td class="px-6 py-4 tabular-nums">{{ $layer->thickness }}</td>
                                    <td class="px-6 py-4 tabular-nums">{{ $layer->width }}</td>
                                    <td class="px-6 py-4 tabular-nums">{{ $layer->angle }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <x-link-button variant="subtle" href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}">
                                                {{ __('Edit') }}
                                            </x-link-button>
                                            <form method="POST" action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('{{ __('Delete this layer?') }}')" class="inline-flex min-h-10 items-center rounded-md px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:text-red-300 dark:hover:bg-red-950 dark:hover:text-red-100 dark:focus:ring-offset-gray-900">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8">
                                        <x-empty-state
                                            :title="__('No layers yet')"
                                            :body="__('Add the first layer, then use Save and add another to build the stack quickly.')"
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

            <div class="mt-6">
                {{ $layers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
