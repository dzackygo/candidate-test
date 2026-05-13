<x-app-layout>
    <x-slot name="header">
        <div>
            <x-breadcrumbs :items="[
                ['label' => __('Suppliers'), 'url' => route('suppliers.index')],
                ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
                ['label' => __('Import conflicts')],
            ]" />
            <h2 class="text-2xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                {{ __('Resolve Import Conflicts') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Choose which values should win before the import changes existing layers.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="warning" class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            <form method="POST" action="{{ route('suppliers.import-conflicts.resolve', $supplier) }}" x-data="{ current: 0, total: {{ count($conflicts) }} }">
                @csrf

                <div class="mb-6 grid gap-4 md:grid-cols-3">
                    <x-metric :label="__('Conflicts')" :value="count($conflicts)" tone="amber" />
                    <x-metric :label="__('Supplier')" :value="$supplier->name" tone="sky" class="md:col-span-2" />
                </div>

                @foreach ($conflicts as $index => $conflict)
                    <section x-cloak x-show="current === {{ $index }}" class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="border-b border-gray-200 p-6 dark:border-gray-700">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                                        {{ __('Conflict') }} <span x-text="current + 1"></span> {{ __('of') }} <span x-text="total"></span>
                                    </p>
                                    <h3 class="mt-1 text-xl font-semibold text-gray-950 dark:text-gray-100">
                                        {{ $conflict['layup_name'] }} / {{ __('Layer') }} {{ $conflict['layer_order'] }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Fields highlighted below have different existing and incoming values.') }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <x-secondary-button type="button" x-on:click="current = Math.max(0, current - 1)" x-bind:disabled="current === 0">
                                        {{ __('Previous') }}
                                    </x-secondary-button>
                                    <x-secondary-button type="button" x-on:click="current = Math.min(total - 1, current + 1)" x-bind:disabled="current === total - 1">
                                        {{ __('Next') }}
                                    </x-secondary-button>
                                </div>
                            </div>

                            <div class="mt-5 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-900">
                                <div class="h-full rounded-full bg-amber-500 transition-all" x-bind:style="`width: ${((current + 1) / total) * 100}%`"></div>
                            </div>
                        </div>

                        <div class="grid gap-6 p-6 lg:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 p-5 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Existing Version') }}</h4>
                                    <span class="rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-800 dark:bg-red-950 dark:text-red-100">{{ __('Current data') }}</span>
                                </div>
                                <dl class="mt-4 space-y-3 text-sm">
                                    @foreach (['thickness', 'width', 'angle'] as $field)
                                        <div class="flex justify-between gap-4 rounded-md px-3 py-2 {{ in_array($field, $conflict['different_fields'], true) ? 'bg-red-50 text-red-900 ring-1 ring-red-200 dark:bg-red-950 dark:text-red-100 dark:ring-red-900' : 'text-gray-700 dark:text-gray-300' }}">
                                            <dt class="font-medium">{{ Str::headline($field) }}</dt>
                                            <dd class="tabular-nums">{{ $conflict['existing'][$field] }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-5 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Incoming Version') }}</h4>
                                    <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-100">{{ __('Imported data') }}</span>
                                </div>
                                <dl class="mt-4 space-y-3 text-sm">
                                    @foreach (['thickness', 'width', 'angle'] as $field)
                                        <div class="flex justify-between gap-4 rounded-md px-3 py-2 {{ in_array($field, $conflict['different_fields'], true) ? 'bg-emerald-50 text-emerald-900 ring-1 ring-emerald-200 dark:bg-emerald-950 dark:text-emerald-100 dark:ring-emerald-900' : 'text-gray-700 dark:text-gray-300' }}">
                                            <dt class="font-medium">{{ Str::headline($field) }}</dt>
                                            <dd class="tabular-nums">{{ $conflict['incoming'][$field] }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </div>

                        <fieldset class="border-t border-gray-200 p-6 dark:border-gray-700">
                            <legend class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Decision for this conflict') }}</legend>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4 text-sm text-gray-800 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900/40">
                                    <input type="radio" name="decisions[{{ $index }}]" value="keep_existing" class="mt-1 border-gray-300 text-gray-900 focus:ring-gray-900" checked>
                                    <span>
                                        <span class="block font-semibold">{{ __('Keep Existing') }}</span>
                                        <span class="mt-1 block text-gray-600 dark:text-gray-400">{{ __('Ignore the imported change for this layer.') }}</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4 text-sm text-gray-800 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900/40">
                                    <input type="radio" name="decisions[{{ $index }}]" value="accept_incoming" class="mt-1 border-gray-300 text-gray-900 focus:ring-gray-900">
                                    <span>
                                        <span class="block font-semibold">{{ __('Accept Incoming') }}</span>
                                        <span class="mt-1 block text-gray-600 dark:text-gray-400">{{ __('Replace the current values with the imported values.') }}</span>
                                    </span>
                                </label>
                            </div>
                        </fieldset>
                    </section>
                @endforeach

                <div class="sticky bottom-0 mt-6 rounded-lg border border-gray-200 bg-white/95 p-4 shadow-sm backdrop-blur dark:border-gray-700 dark:bg-gray-800/95">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Your choices are applied only after you click Apply Resolutions.') }}
                        </p>
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                            <x-link-button variant="secondary" href="{{ route('suppliers.show', $supplier) }}">
                                {{ __('Cancel') }}
                            </x-link-button>
                            <x-primary-button>{{ __('Apply Resolutions') }}</x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
