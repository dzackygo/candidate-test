<?php

namespace App\Services;

use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierImportConflictService
{
    /**
     * @param  array<string, mixed>  $draft
     * @param  array<int, string>  $decisions
     * @return array<string, int>
     */
    public function resolve(Supplier $supplier, array $draft, array $decisions): array
    {
        $payload = $draft['payload'];
        $conflicts = $draft['conflicts'];
        $decisionByLayer = $this->decisionByLayer($conflicts, $decisions);
        $appliedLayers = 0;

        DB::transaction(function () use ($supplier, $payload, $decisionByLayer, &$appliedLayers) {
            foreach ($payload['layups'] as $incomingLayup) {
                /** @var CltLayup $layup */
                $layup = $supplier->layups()->firstOrCreate([
                    'name' => $incomingLayup['name'],
                ]);

                foreach ($incomingLayup['layers'] as $incomingLayer) {
                    $key = $this->layerKey($incomingLayup['name'], $incomingLayer['layer_order']);
                    $existingLayer = $layup->layers()->where('layer_order', $incomingLayer['layer_order'])->first();

                    if ($existingLayer !== null && ($decisionByLayer[$key] ?? null) === 'keep_existing') {
                        continue;
                    }

                    $layup->layers()->updateOrCreate(
                        ['layer_order' => $incomingLayer['layer_order']],
                        [
                            'thickness' => $incomingLayer['thickness'],
                            'width' => $incomingLayer['width'],
                            'angle' => $incomingLayer['angle'],
                        ]
                    );

                    $appliedLayers++;
                }
            }
        });

        return ['applied_layers' => $appliedLayers];
    }

    /**
     * @param  array<int, array<string, mixed>>  $conflicts
     * @param  array<int, string>  $decisions
     * @return array<string, string>
     */
    private function decisionByLayer(array $conflicts, array $decisions): array
    {
        $mapped = [];

        foreach ($conflicts as $index => $conflict) {
            $mapped[$this->layerKey($conflict['layup_name'], $conflict['layer_order'])] = $decisions[$index] ?? 'keep_existing';
        }

        return $mapped;
    }

    private function layerKey(string $layupName, int $layerOrder): string
    {
        return $layupName.'|'.$layerOrder;
    }
}
