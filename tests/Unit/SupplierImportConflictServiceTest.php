<?php

namespace Tests\Unit;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\SupplierImportConflictService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierImportConflictServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_accepts_incoming_values_for_selected_conflicts(): void
    {
        [$supplier, $layer] = $this->existingConflictFixture();

        $result = app(SupplierImportConflictService::class)->resolve(
            $supplier,
            $this->draftPayload(),
            [0 => 'accept_incoming'],
        );

        $this->assertSame(2, $result['applied_layers']);
        $this->assertSame('30.000', $layer->fresh()->thickness);
        $this->assertSame('1300.000', $layer->fresh()->width);
        $this->assertSame('90.000', $layer->fresh()->angle);
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layer->layup_id,
            'layer_order' => 2,
            'thickness' => 40,
            'width' => 1400,
            'angle' => 0,
        ]);
    }

    public function test_it_keeps_existing_values_for_selected_conflicts_while_applying_non_conflicting_layers(): void
    {
        [$supplier, $layer] = $this->existingConflictFixture();

        $result = app(SupplierImportConflictService::class)->resolve(
            $supplier,
            $this->draftPayload(),
            [0 => 'keep_existing'],
        );

        $this->assertSame(1, $result['applied_layers']);
        $this->assertSame('20.000', $layer->fresh()->thickness);
        $this->assertSame('1200.000', $layer->fresh()->width);
        $this->assertSame('0.000', $layer->fresh()->angle);
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layer->layup_id,
            'layer_order' => 2,
        ]);
    }

    public function test_missing_decisions_default_to_keep_existing(): void
    {
        [$supplier, $layer] = $this->existingConflictFixture();

        app(SupplierImportConflictService::class)->resolve(
            $supplier,
            $this->draftPayload(),
            [],
        );

        $this->assertSame('20.000', $layer->fresh()->thickness);
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layer->layup_id,
            'layer_order' => 2,
        ]);
    }

    /**
     * @return array{0: Supplier, 1: CltLayer}
     */
    private function existingConflictFixture(): array
    {
        $supplier = Supplier::factory()->create();
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
    private function draftPayload(): array
    {
        return [
            'status' => 'conflicts',
            'payload' => [
                'layups' => [
                    [
                        'name' => 'Wall Layup',
                        'layers' => [
                            [
                                'layer_order' => 1,
                                'thickness' => '30.000',
                                'width' => '1300.000',
                                'angle' => '90.000',
                            ],
                            [
                                'layer_order' => 2,
                                'thickness' => '40.000',
                                'width' => '1400.000',
                                'angle' => '0.000',
                            ],
                        ],
                    ],
                ],
            ],
            'conflicts' => [
                [
                    'layup_name' => 'Wall Layup',
                    'layer_order' => 1,
                    'different_fields' => ['thickness', 'width', 'angle'],
                    'existing' => [
                        'thickness' => '20.000',
                        'width' => '1200.000',
                        'angle' => '0.000',
                    ],
                    'incoming' => [
                        'thickness' => '30.000',
                        'width' => '1300.000',
                        'angle' => '90.000',
                    ],
                ],
            ],
        ];
    }
}
