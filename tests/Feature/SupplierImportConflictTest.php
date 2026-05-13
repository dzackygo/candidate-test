<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SupplierImportConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_redirects_to_conflict_resolution_when_layer_values_differ(): void
    {
        $user = User::factory()->create();
        [$supplier, $layer] = $this->existingLayer();

        $response = $this->actingAs($user)->post(route('suppliers.import', $supplier), [
            'import_file' => UploadedFile::fake()->createWithContent('supplier.json', json_encode($this->conflictingPayload())),
        ]);

        $response->assertRedirect(route('suppliers.import-conflicts.show', $supplier));
        $response->assertSessionHas("supplier_import.{$supplier->id}.conflicts.0.layer_order", 1);
        $this->assertSame('20.000', $layer->fresh()->thickness);
    }

    public function test_user_can_accept_incoming_conflict_values(): void
    {
        $user = User::factory()->create();
        [$supplier, $layer] = $this->existingLayer();

        $this->actingAs($user)->post(route('suppliers.import', $supplier), [
            'import_file' => UploadedFile::fake()->createWithContent('supplier.json', json_encode($this->conflictingPayload())),
        ]);

        $response = $this->actingAs($user)->post(route('suppliers.import-conflicts.resolve', $supplier), [
            'decisions' => [
                0 => 'accept_incoming',
            ],
        ]);

        $response->assertRedirect(route('suppliers.show', $supplier));
        $this->assertSame('30.000', $layer->fresh()->thickness);
        $this->assertSame('1300.000', $layer->fresh()->width);
        $this->assertSame('90.000', $layer->fresh()->angle);
    }

    public function test_conflict_resolution_page_displays_existing_and_incoming_versions(): void
    {
        $user = User::factory()->create();
        [$supplier] = $this->existingLayer();

        $this->actingAs($user)->post(route('suppliers.import', $supplier), [
            'import_file' => UploadedFile::fake()->createWithContent('supplier.json', json_encode($this->conflictingPayload())),
        ]);

        $response = $this->actingAs($user)->get(route('suppliers.import-conflicts.show', $supplier));

        $response->assertOk();
        $response->assertSee('Existing Version');
        $response->assertSee('Incoming Version');
        $response->assertSee('20.000');
        $response->assertSee('30.000');
        $response->assertSee('Keep Existing');
        $response->assertSee('Accept Incoming');
    }

    public function test_user_can_keep_existing_conflict_values(): void
    {
        $user = User::factory()->create();
        [$supplier, $layer] = $this->existingLayer();

        $this->actingAs($user)->post(route('suppliers.import', $supplier), [
            'import_file' => UploadedFile::fake()->createWithContent('supplier.json', json_encode($this->conflictingPayload())),
        ]);

        $response = $this->actingAs($user)->post(route('suppliers.import-conflicts.resolve', $supplier), [
            'decisions' => [
                0 => 'keep_existing',
            ],
        ]);

        $response->assertRedirect(route('suppliers.show', $supplier));
        $this->assertSame('20.000', $layer->fresh()->thickness);
        $this->assertSame('1200.000', $layer->fresh()->width);
        $this->assertSame('0.000', $layer->fresh()->angle);
    }

    /**
     * @return array{0: Supplier, 1: CltLayer}
     */
    private function existingLayer(): array
    {
        $supplier = Supplier::factory()->create(['name' => 'Conflict Supplier']);
        $layup = CltLayup::factory()->for($supplier)->create(['name' => 'Wall Layup']);
        $layer = CltLayer::factory()->for($layup, 'layup')->create([
            'layer_order' => 1,
            'thickness' => 20,
            'width' => 1200,
            'angle' => 0,
        ]);

        return [$supplier, $layer];
    }

    /**
     * @return array<string, mixed>
     */
    private function conflictingPayload(): array
    {
        return [
            'supplier' => ['name' => 'Conflict Supplier'],
            'layups' => [
                [
                    'name' => 'Wall Layup',
                    'layers' => [
                        [
                            'layer_order' => 1,
                            'thickness' => 30,
                            'width' => 1300,
                            'angle' => 90,
                        ],
                    ],
                ],
            ],
        ];
    }
}
