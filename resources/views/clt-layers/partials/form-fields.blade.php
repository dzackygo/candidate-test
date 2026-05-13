<div>
    <x-input-label for="layer_order" :value="__('Layer order')" />
    <x-text-input id="layer_order" name="layer_order" type="number" min="1" step="1" class="mt-1 block w-full tabular-nums" :value="old('layer_order', $layer->layer_order ?? '')" required />
    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Use a unique order within this layup, starting from 1.') }}</p>
    <x-input-error class="mt-2" :messages="$errors->get('layer_order')" />
</div>

<div class="grid gap-6 md:grid-cols-3">
    <div>
        <x-input-label for="thickness" :value="__('Thickness')" />
        <x-text-input id="thickness" name="thickness" type="number" min="0" step="0.001" class="mt-1 block w-full tabular-nums" :value="old('thickness', $layer->thickness ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('thickness')" />
    </div>

    <div>
        <x-input-label for="width" :value="__('Width')" />
        <x-text-input id="width" name="width" type="number" min="0" step="0.001" class="mt-1 block w-full tabular-nums" :value="old('width', $layer->width ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('width')" />
    </div>

    <div>
        <x-input-label for="angle" :value="__('Angle')" />
        <x-text-input id="angle" name="angle" type="number" step="0.001" class="mt-1 block w-full tabular-nums" :value="old('angle', $layer->angle ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('angle')" />
    </div>
</div>

<x-alert type="info">
    {{ __('Tip: conflicts are checked by layer order. If an imported layer has the same order but different values, the app will ask you to resolve it first.') }}
</x-alert>
