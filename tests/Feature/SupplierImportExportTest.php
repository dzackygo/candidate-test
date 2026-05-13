<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SupplierImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_export_supplier_with_layups_and_layers(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['name' => 'Export Supplier']);
        $layup = CltLayup::factory()->for($supplier)->create(['name' => 'Wall Layup']);
        CltLayer::factory()->for($layup, 'layup')->create([
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 1200,
            'angle' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('suppliers.export', $supplier));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $response->assertJsonPath('supplier.name', 'Export Supplier');
        $response->assertJsonPath('layups.0.name', 'Wall Layup');
        $response->assertJsonPath('layups.0.layers.0.layer_order', 1);
        $response->assertJsonPath('layups.0.layers.0.thickness', '20.000');
    }

    public function test_authenticated_user_can_import_supplier_payload_without_conflicts(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['name' => 'Import Target']);
        $payload = [
            'supplier' => ['name' => 'Any Payload Name'],
            'layups' => [
                [
                    'name' => 'Imported Layup',
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

        $response = $this->actingAs($user)->post(route('suppliers.import', $supplier), [
            'import_file' => UploadedFile::fake()->createWithContent('supplier.json', json_encode($payload)),
        ]);

        $response->assertRedirect(route('suppliers.show', $supplier));
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Imported Layup',
        ]);
        $this->assertDatabaseHas('clt_layers', [
            'layer_order' => 1,
            'thickness' => 24.5,
            'width' => 1000.25,
            'angle' => 90,
        ]);
    }
}
