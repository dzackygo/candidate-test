<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayerCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_layers_for_layup(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['name' => 'Panel Source']);
        $layup = CltLayup::factory()->for($supplier)->create(['name' => 'Wall Panel']);
        CltLayer::factory()->for($layup, 'layup')->create([
            'layer_order' => 2,
            'thickness' => 32,
            'width' => 1200,
            'angle' => 90,
        ]);

        $response = $this->actingAs($user)->get(route('suppliers.layups.layers.index', [$supplier, $layup]));

        $response->assertOk();
        $response->assertSee('Wall Panel');
        $response->assertSee('32.000');
        $response->assertSee('90.000');
    }

    public function test_authenticated_user_can_create_layer_under_layup(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create();

        $response = $this->actingAs($user)->post(route('suppliers.layups.layers.store', [$supplier, $layup]), [
            'layer_order' => 1,
            'thickness' => 25.5,
            'width' => 900.25,
            'angle' => 0,
        ]);

        $response->assertRedirect(route('suppliers.layups.layers.index', [$supplier, $layup]));
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 25.5,
            'width' => 900.25,
            'angle' => 0,
        ]);
    }

    public function test_authenticated_user_can_create_layer_and_continue_adding_layers(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create();

        $response = $this->actingAs($user)->post(route('suppliers.layups.layers.store', [$supplier, $layup]), [
            'layer_order' => 1,
            'thickness' => 25.5,
            'width' => 900.25,
            'angle' => 0,
            'after_save' => 'add_another',
        ]);

        $response->assertRedirect(route('suppliers.layups.layers.create', [$supplier, $layup]));
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 1,
        ]);
    }

    public function test_authenticated_user_can_update_layup_layer(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create();
        $layer = CltLayer::factory()->for($layup, 'layup')->create(['layer_order' => 1]);

        $response = $this->actingAs($user)->put(route('suppliers.layups.layers.update', [$supplier, $layup, $layer]), [
            'layer_order' => 3,
            'thickness' => 30,
            'width' => 1000,
            'angle' => 45,
        ]);

        $response->assertRedirect(route('suppliers.layups.layers.index', [$supplier, $layup]));
        $this->assertDatabaseHas('clt_layers', [
            'id' => $layer->id,
            'layer_order' => 3,
            'thickness' => 30,
            'width' => 1000,
            'angle' => 45,
        ]);
    }

    public function test_authenticated_user_can_delete_layup_layer(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create();
        $layer = CltLayer::factory()->for($layup, 'layup')->create();

        $response = $this->actingAs($user)->delete(route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]));

        $response->assertRedirect(route('suppliers.layups.layers.index', [$supplier, $layup]));
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }
}
