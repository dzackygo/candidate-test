<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumbs :items="[
                ['label' => __('Suppliers'), 'url' => route('suppliers.index')],
                ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
                ['label' => __('Layups'), 'url' => route('suppliers.layups.index', $supplier)],
                ['label' => $layup->name, 'url' => route('suppliers.layups.show', [$supplier, $layup])],
                ['label' => __('Layers'), 'url' => route('suppliers.layups.layers.index', [$supplier, $layup])],
                ['label' => __('Create')],
            ]" />
            <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                {{ __('Create Layer') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Add layer dimensions and orientation. Use Save and add another for rapid stack entry.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            <x-panel class="p-6">
                <form method="POST" action="{{ route('suppliers.layups.layers.store', [$supplier, $layup]) }}" class="space-y-6">
                    @csrf

                    @include('clt-layers.partials.form-fields')

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <x-link-button variant="secondary" href="{{ route('suppliers.layups.layers.index', [$supplier, $layup]) }}">
                            {{ __('Cancel') }}
                        </x-link-button>
                        <x-secondary-button type="submit" name="after_save" value="add_another">
                            {{ __('Save and Add Another') }}
                        </x-secondary-button>
                        <x-primary-button>{{ __('Save Layer') }}</x-primary-button>
                    </div>
                </form>
            </x-panel>
        </div>
    </div>
</x-app-layout>
