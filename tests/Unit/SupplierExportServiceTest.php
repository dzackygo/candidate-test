<?php

namespace Tests\Unit;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\SupplierExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierExportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exports_supplier_with_nested_layups_and_layers(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Export Source']);
        $layup = CltLayup::factory()->for($supplier)->create(['name' => 'Wall Layup']);
        CltLayer::factory()->for($layup, 'layup')->create([
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 1200,
            'angle' => 90,
        ]);

        $payload = app(SupplierExportService::class)->export($supplier);

        $this->assertSame('Export Source', $payload['supplier']['name']);
        $this->assertSame('Wall Layup', $payload['layups'][0]['name']);
        $this->assertSame(1, $payload['layups'][0]['layers'][0]['layer_order']);
        $this->assertSame('20.000', $payload['layups'][0]['layers'][0]['thickness']);
        $this->assertSame('1200.000', $payload['layups'][0]['layers'][0]['width']);
        $this->assertSame('90.000', $payload['layups'][0]['layers'][0]['angle']);
    }

    public function test_it_does_not_export_other_supplier_data(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Target Supplier']);
        $otherSupplier = Supplier::factory()->create(['name' => 'Other Supplier']);
        $targetLayup = CltLayup::factory()->for($supplier)->create(['name' => 'Target Layup']);
        $otherLayup = CltLayup::factory()->for($otherSupplier)->create(['name' => 'Other Layup']);
        CltLayer::factory()->for($targetLayup, 'layup')->create(['layer_order' => 1]);
        CltLayer::factory()->for($otherLayup, 'layup')->create(['layer_order' => 1]);

        $payload = app(SupplierExportService::class)->export($supplier);

        $this->assertSame('Target Supplier', $payload['supplier']['name']);
        $this->assertCount(1, $payload['layups']);
        $this->assertSame('Target Layup', $payload['layups'][0]['name']);
    }
}
