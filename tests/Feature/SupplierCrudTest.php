<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_suppliers(): void
    {
        $user = User::factory()->create();
        Supplier::factory()->create(['name' => 'Mass Timber Works']);

        $response = $this->actingAs($user)->get(route('suppliers.index'));

        $response->assertOk();
        $response->assertSee('Mass Timber Works');
    }

    public function test_authenticated_user_can_create_supplier(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('suppliers.store'), [
            'name' => 'Pacific CLT',
        ]);

        $supplier = Supplier::where('name', 'Pacific CLT')->firstOrFail();

        $response->assertRedirect(route('suppliers.show', $supplier));
        $this->assertDatabaseHas('suppliers', ['name' => 'Pacific CLT']);
    }

    public function test_authenticated_user_can_update_supplier(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create(['name' => 'Old Supplier']);

        $response = $this->actingAs($user)->put(route('suppliers.update', $supplier), [
            'name' => 'Updated Supplier',
        ]);

        $response->assertRedirect(route('suppliers.show', $supplier));
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'Updated Supplier']);
    }

    public function test_authenticated_user_can_delete_supplier(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($user)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
