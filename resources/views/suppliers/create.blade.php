<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumbs :items="[
                ['label' => __('Suppliers'), 'url' => route('suppliers.index')],
                ['label' => __('Create')],
            ]" />
            <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                {{ __('Create Supplier') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Start with the supplier, then add layups and layers from the detail page.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-panel class="p-6">
                <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Supplier name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Example: Pacific CLT" />
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Use the company or material supplier name users will recognize.') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <x-link-button variant="secondary" href="{{ route('suppliers.index') }}">
                            {{ __('Cancel') }}
                        </x-link-button>
                        <x-primary-button>{{ __('Create and Open Supplier') }}</x-primary-button>
                    </div>
                </form>
            </x-panel>
        </div>
    </div>
</x-app-layout>
