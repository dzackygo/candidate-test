<?php

namespace Tests\Feature;

use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_layups_for_supplier(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['name' => 'CLT Source']);
        CltLayup::factory()->for($supplier)->create(['name' => 'Roof Panel']);

        $response = $this->actingAs($user)->get(route('suppliers.layups.index', $supplier));

        $response->assertOk();
        $response->assertSee('CLT Source');
        $response->assertSee('Roof Panel');
    }

    public function test_authenticated_user_can_create_layup_under_supplier(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($user)->post(route('suppliers.layups.store', $supplier), [
            'name' => 'Floor Assembly',
        ]);

        $layup = CltLayup::where('name', 'Floor Assembly')->firstOrFail();

        $response->assertRedirect(route('suppliers.layups.show', [$supplier, $layup]));
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Floor Assembly',
        ]);
    }

    public function test_authenticated_user_can_update_supplier_layup(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create(['name' => 'Old Layup']);

        $response = $this->actingAs($user)->put(route('suppliers.layups.update', [$supplier, $layup]), [
            'name' => 'Updated Layup',
        ]);

        $response->assertRedirect(route('suppliers.layups.show', [$supplier, $layup]));
        $this->assertDatabaseHas('clt_layups', [
            'id' => $layup->id,
            'supplier_id' => $supplier->id,
            'name' => 'Updated Layup',
        ]);
    }

    public function test_authenticated_user_can_delete_supplier_layup(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $layup = CltLayup::factory()->for($supplier)->create();

        $response = $this->actingAs($user)->delete(route('suppliers.layups.destroy', [$supplier, $layup]));

        $response->assertRedirect(route('suppliers.layups.index', $supplier));
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }
}
