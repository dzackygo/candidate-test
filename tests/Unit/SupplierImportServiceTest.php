<?php

namespace Tests\Unit;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\SupplierImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SupplierImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_new_layups_and_layers(): void
    {
        $supplier = Supplier::factory()->create();
        $payload = [
            'layups' => [
                [
                    'name' => 'Imported Wall',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 24.5,
                            'width' => 1000.25,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ];

        $result = app(SupplierImportService::class)->import($supplier, $payload);

        $this->assertSame('imported', $result['status']);
        $this->assertSame(1, $result['layup_count']);
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Imported Wall',
        ]);
        $this->assertDatabaseHas('clt_layers', [
            'layer_order' => 1,
            'thickness' => 24.5,
            'width' => 1000.25,
            'angle' => 90,
        ]);
    }

    public function test_it_adds_missing_layers_to_an_existing_layup_without_creating_a_duplicate_layup(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create(['name' => 'Existing Layup']);
        CltLayer::factory()->for($layup, 'layup')->create([
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 1200,
            'angle' => 0,
        ]);

        $result = app(SupplierImportService::class)->import($supplier, [
            'layups' => [
                [
                    'name' => 'Existing Layup',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 20,
                            'width' => 1200,
                            'angle' => 0,
                        ],
                        [
                            'layer_order' => 2,
                            'thickness' => 30,
                            'width' => 1300,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame('imported', $result['status']);
        $this->assertSame(1, $supplier->layups()->where('name', 'Existing Layup')->count());
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 2,
            'thickness' => 30,
            'width' => 1300,
            'angle' => 90,
        ]);
    }

    public function test_it_returns_conflicts_without_mutating_existing_layer_values(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create(['name' => 'Wall Layup']);
        $layer = CltLayer::factory()->for($layup, 'layup')->create([
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 1200,
            'angle' => 0,
        ]);

        $result = app(SupplierImportService::class)->import($supplier, [
            'layups' => [
                [
                    'name' => 'Wall Layup',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 25,
                            'width' => 1200,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame('conflicts', $result['status']);
        $this->assertSame(['thickness', 'angle'], $result['conflicts'][0]['different_fields']);
        $this->assertSame('20.000', $layer->fresh()->thickness);
        $this->assertSame('0.000', $layer->fresh()->angle);
    }

    public function test_it_rejects_payloads_without_layups(): void
    {
        $this->expectException(ValidationException::class);

        app(SupplierImportService::class)->import(Supplier::factory()->create(), [
            'supplier' => ['name' => 'Broken Payload'],
        ]);
    }
}
