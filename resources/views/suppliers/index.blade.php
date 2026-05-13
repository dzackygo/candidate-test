<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-breadcrumbs :items="[
                    ['label' => __('Suppliers')],
                ]" />
                <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                    {{ __('Suppliers') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Manage supplier records, then drill into their layups and layers.') }}
                </p>
            </div>
            <x-link-button href="{{ route('suppliers.create') }}">
                {{ __('Create Supplier') }}
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
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($suppliers as $supplier)
                        <div class="grid gap-4 p-5 md:grid-cols-[1fr_auto] md:items-center">
                            <div class="min-w-0">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="block truncate text-lg font-semibold text-gray-950 hover:text-sky-700 dark:text-gray-100 dark:hover:text-sky-300">
                                    {{ $supplier->name }}
                                </a>
                                <div class="mt-2 flex flex-wrap gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="rounded-md bg-gray-100 px-2 py-1 dark:bg-gray-900">{{ $supplier->layups_count }} {{ __('layups') }}</span>
                                    <span class="rounded-md bg-gray-100 px-2 py-1 dark:bg-gray-900">{{ $supplier->layers_count }} {{ __('layers') }}</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 md:justify-end">
                                <x-link-button variant="secondary" href="{{ route('suppliers.show', $supplier) }}">
                                    {{ __('Open') }}
                                </x-link-button>
                                <x-link-button variant="subtle" href="{{ route('suppliers.edit', $supplier) }}">
                                    {{ __('Edit') }}
                                </x-link-button>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('{{ __('Delete this supplier and all related layups/layers?') }}')" class="inline-flex min-h-10 items-center rounded-md px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:text-red-300 dark:hover:bg-red-950 dark:hover:text-red-100 dark:focus:ring-offset-gray-900">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6">
                            <x-empty-state
                                :title="__('No suppliers yet')"
                                :body="__('Create the first supplier, then add layups and layers under it. You can also import JSON after the supplier exists.')"
                                :action-label="__('Create Supplier')"
                                :action-url="route('suppliers.create')"
                            />
                        </div>
                    @endforelse
                </div>
            </x-panel>

            <div class="mt-6">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
