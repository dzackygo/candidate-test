<x-app-layout>
    @php
        $layupCount = $supplier->layups->count();
        $layerCount = $supplier->layups->sum(fn ($layup) => $layup->layers->count());
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <x-breadcrumbs :items="[
                    ['label' => __('Suppliers'), 'url' => route('suppliers.index')],
                    ['label' => $supplier->name],
                ]" />
                <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                    {{ $supplier->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Review the supplier hierarchy, manage layups, or exchange data with JSON import/export.') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-link-button variant="secondary" href="{{ route('suppliers.edit', $supplier) }}">
                    {{ __('Edit Supplier') }}
                </x-link-button>
                <x-link-button href="{{ route('suppliers.layups.create', $supplier) }}">
                    {{ __('Add Layup') }}
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
                <x-metric :label="__('Layups')" :value="$layupCount" tone="sky" />
                <x-metric :label="__('Layers')" :value="$layerCount" tone="emerald" />
                <x-metric :label="__('Import status')" :value="$layupCount > 0 ? __('Ready') : __('Empty')" tone="amber" />
            </div>

            <div class="mb-6 grid gap-6 lg:grid-cols-2">
                <x-panel class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Export JSON') }}</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Download this supplier together with every layup and layer. The file can be imported into another supplier later.') }}
                            </p>
                        </div>
                        <span class="rounded-md bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-800 dark:bg-sky-950 dark:text-sky-100">JSON</span>
                    </div>
                    <div class="mt-5">
                        <x-link-button href="{{ route('suppliers.export', $supplier) }}">
                            {{ __('Download Export') }}
                        </x-link-button>
                    </div>
                </x-panel>

                <x-panel class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Import JSON') }}</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Import layups and layers into this supplier. If an existing layer differs, you will review the conflict before changes apply.') }}
                            </p>
                        </div>
                        <span class="rounded-md bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950 dark:text-amber-100">{{ __('Review safe') }}</span>
                    </div>
                    <form method="POST" action="{{ route('suppliers.import', $supplier) }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="import_file" :value="__('JSON file')" />
                            <input id="import_file" name="import_file" type="file" accept="application/json,.json" required class="mt-1 block w-full rounded-md border border-gray-300 text-sm text-gray-700 file:mr-4 file:border-0 file:bg-gray-900 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:file:bg-gray-100 dark:file:text-gray-900" />
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Expected structure: supplier, layups, and nested layers.') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('import_file')" />
                        </div>
                        <x-primary-button>{{ __('Import and Review') }}</x-primary-button>
                    </form>
                </x-panel>
            </div>

            <x-panel class="p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Layup hierarchy') }}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Open a layup to manage its layer stack.') }}</p>
                    </div>
                    <x-link-button variant="secondary" href="{{ route('suppliers.layups.index', $supplier) }}">
                        {{ __('View All Layups') }}
                    </x-link-button>
                </div>

                <div class="mt-6 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($supplier->layups as $layup)
                        <div class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="font-semibold text-gray-950 hover:text-sky-700 dark:text-gray-100 dark:hover:text-sky-300">{{ $layup->name }}</a>
                                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $layup->layers->count() }} {{ __('layers') }}
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <x-link-button variant="subtle" href="{{ route('suppliers.layups.layers.index', [$supplier, $layup]) }}">
                                    {{ __('Layers') }}
                                </x-link-button>
                                <x-link-button variant="subtle" href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}">
                                    {{ __('Edit') }}
                                </x-link-button>
                            </div>
                        </div>
                    @empty
                        <x-empty-state
                            :title="__('No layups yet')"
                            :body="__('Add a layup to start building the Supplier -> Layups -> Layers structure.')"
                            :action-label="__('Add Layup')"
                            :action-url="route('suppliers.layups.create', $supplier)"
                        />
                    @endforelse
                </div>
            </x-panel>
        </div>
    </div>
</x-app-layout>
