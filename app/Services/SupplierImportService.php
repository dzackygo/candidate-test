<?php

namespace App\Services;

use App\Contracts\SupplierImportServiceInterface;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierImportService implements SupplierImportServiceInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function import(Supplier $supplier, array $payload): array
    {
        $normalized = $this->normalizePayload($payload);
        $conflicts = $this->detectConflicts($supplier, $normalized);

        if ($conflicts !== []) {
            return [
                'status' => 'conflicts',
                'payload' => $normalized,
                'conflicts' => $conflicts,
            ];
        }

        DB::transaction(function () use ($supplier, $normalized) {
            foreach ($normalized['layups'] as $incomingLayup) {
                /** @var CltLayup $layup */
                $layup = $supplier->layups()->firstOrCreate([
                    'name' => $incomingLayup['name'],
                ]);

                foreach ($incomingLayup['layers'] as $incomingLayer) {
                    $layup->layers()->updateOrCreate(
                        ['layer_order' => $incomingLayer['layer_order']],
                        [
                            'thickness' => $incomingLayer['thickness'],
                            'width' => $incomingLayer['width'],
                            'angle' => $incomingLayer['angle'],
                        ]
                    );
                }
            }
        });

        return [
            'status' => 'imported',
            'layup_count' => count($normalized['layups']),
        ];
    }

    /**
     * @param  array{layups: array<int, array{name: string, layers: array<int, array{layer_order: int, thickness: mixed, width: mixed, angle: mixed}>}>}  $payload
     * @return array<int, array<string, mixed>>
     */
    private function detectConflicts(Supplier $supplier, array $payload): array
    {
        $supplier->load('layups.layers');
        $conflicts = [];

        foreach ($payload['layups'] as $incomingLayup) {
            $existingLayup = $supplier->layups->firstWhere('name', $incomingLayup['name']);

            if (! $existingLayup instanceof CltLayup) {
                continue;
            }

            foreach ($incomingLayup['layers'] as $incomingLayer) {
                $existingLayer = $existingLayup->layers->firstWhere('layer_order', $incomingLayer['layer_order']);

                if ($existingLayer === null) {
                    continue;
                }

                $differentFields = [];

                foreach (['thickness', 'width', 'angle'] as $field) {
                    if ($this->decimalString($existingLayer->{$field}) !== $this->decimalString($incomingLayer[$field])) {
                        $differentFields[] = $field;
                    }
                }

                if ($differentFields === []) {
                    continue;
                }

                $conflicts[] = [
                    'layup_name' => $incomingLayup['name'],
                    'layer_order' => $incomingLayer['layer_order'],
                    'different_fields' => $differentFields,
                    'existing' => [
                        'thickness' => $this->decimalString($existingLayer->thickness),
                        'width' => $this->decimalString($existingLayer->width),
                        'angle' => $this->decimalString($existingLayer->angle),
                    ],
                    'incoming' => [
                        'thickness' => $this->decimalString($incomingLayer['thickness']),
                        'width' => $this->decimalString($incomingLayer['width']),
                        'angle' => $this->decimalString($incomingLayer['angle']),
                    ],
                ];
            }
        }

        return $conflicts;
    }

    private function decimalString(mixed $value): string
    {
        return number_format((float) $value, 3, '.', '');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{layups: array<int, array{name: string, layers: array<int, array{layer_order: int, thickness: mixed, width: mixed, angle: mixed}>}>}
     */
    protected function normalizePayload(array $payload): array
    {
        if (! isset($payload['layups']) || ! is_array($payload['layups'])) {
            throw ValidationException::withMessages([
                'import_file' => 'The import file must contain a layups array.',
            ]);
        }

        $layups = [];

        foreach ($payload['layups'] as $layupIndex => $layup) {
            if (! is_array($layup) || blank($layup['name'] ?? null)) {
                throw ValidationException::withMessages([
                    'import_file' => "Layup #{$layupIndex} must include a name.",
                ]);
            }

            if (! isset($layup['layers']) || ! is_array($layup['layers'])) {
                throw ValidationException::withMessages([
                    'import_file' => "Layup {$layup['name']} must include a layers array.",
                ]);
            }

            $layers = [];

            foreach ($layup['layers'] as $layerIndex => $layer) {
                if (! is_array($layer)) {
                    throw ValidationException::withMessages([
                        'import_file' => "Layer #{$layerIndex} in {$layup['name']} is invalid.",
                    ]);
                }

                foreach (['layer_order', 'thickness', 'width', 'angle'] as $field) {
                    if (! array_key_exists($field, $layer) || ! is_numeric($layer[$field])) {
                        throw ValidationException::withMessages([
                            'import_file' => "Layer #{$layerIndex} in {$layup['name']} must include numeric {$field}.",
                        ]);
                    }
                }

                $layers[] = [
                    'layer_order' => (int) $layer['layer_order'],
                    'thickness' => $this->decimalString($layer['thickness']),
                    'width' => $this->decimalString($layer['width']),
                    'angle' => $this->decimalString($layer['angle']),
                ];
            }

            $layups[] = [
                'name' => (string) $layup['name'],
                'layers' => $layers,
            ];
        }

        return ['layups' => $layups];
    }
}
