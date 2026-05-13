<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumbs :items="[
                ['label' => __('Suppliers'), 'url' => route('suppliers.index')],
                ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
                ['label' => __('Layups'), 'url' => route('suppliers.layups.index', $supplier)],
                ['label' => __('Create')],
            ]" />
            <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                {{ __('Create Layup') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Name the layup assembly. You will add its layers next.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-panel class="p-6">
                <form method="POST" action="{{ route('suppliers.layups.store', $supplier) }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Layup name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Example: Wall 5 Ply" />
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Names must be unique within this supplier.') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <x-link-button variant="secondary" href="{{ route('suppliers.layups.index', $supplier) }}">
                            {{ __('Cancel') }}
                        </x-link-button>
                        <x-primary-button>{{ __('Create and Add Layers') }}</x-primary-button>
                    </div>
                </form>
            </x-panel>
        </div>
    </div>
</x-app-layout>
