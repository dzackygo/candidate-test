<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumbs :items="[
                ['label' => __('Suppliers'), 'url' => route('suppliers.index')],
                ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
                ['label' => __('Layups'), 'url' => route('suppliers.layups.index', $supplier)],
                ['label' => $layup->name, 'url' => route('suppliers.layups.show', [$supplier, $layup])],
                ['label' => __('Layers'), 'url' => route('suppliers.layups.layers.index', [$supplier, $layup])],
                ['label' => __('Layer :order', ['order' => $layer->layer_order])],
            ]" />
            <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                {{ __('Edit Layer') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Adjust this layer without leaving the layup context.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-panel class="p-6">
                <form method="POST" action="{{ route('suppliers.layups.layers.update', [$supplier, $layup, $layer]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @include('clt-layers.partials.form-fields')

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <x-link-button variant="secondary" href="{{ route('suppliers.layups.layers.index', [$supplier, $layup]) }}">
                            {{ __('Cancel') }}
                        </x-link-button>
                        <x-primary-button>{{ __('Save Layer') }}</x-primary-button>
                    </div>
                </form>
            </x-panel>
        </div>
    </div>
</x-app-layout>
