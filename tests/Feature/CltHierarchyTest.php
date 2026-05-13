<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_layup_and_layer_relationships_are_nested(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Nord Timber']);
        $layup = CltLayup::factory()->for($supplier)->create(['name' => 'Wall 5 Ply']);
        $layer = CltLayer::factory()->for($layup, 'layup')->create([
            'layer_order' => 1,
            'thickness' => 20.5,
            'width' => 1200.25,
            'angle' => 90,
        ]);

        $this->assertTrue($supplier->layups->contains($layup));
        $this->assertTrue($layup->layers->contains($layer));
        $this->assertTrue($layer->layup->is($layup));
        $this->assertTrue($layup->supplier->is($supplier));
    }

    public function test_deleting_supplier_cascades_to_layups_and_layers(): void
    {
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create();
        $layer = CltLayer::factory()->for($layup, 'layup')->create();

        $supplier->delete();

        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }
}
