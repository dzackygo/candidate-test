<?php

namespace App\Services;

use App\Contracts\SupplierExportServiceInterface;
use App\Models\Supplier;

class SupplierExportService implements SupplierExportServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function export(Supplier $supplier): array
    {
        $supplier->load(['layups.layers']);

        return [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ],
            'layups' => $supplier->layups->map(fn ($layup) => [
                'id' => $layup->id,
                'name' => $layup->name,
                'layers' => $layup->layers->map(fn ($layer) => [
                    'id' => $layer->id,
                    'layer_order' => $layer->layer_order,
                    'thickness' => $layer->thickness,
                    'width' => $layer->width,
                    'angle' => $layer->angle,
                ])->values()->all(),
            ])->values()->all(),
        ];
    }
}
