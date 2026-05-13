<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCltLayerRequest;
use App\Http\Requests\UpdateCltLayerRequest;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CltLayerController extends Controller
{
    public function index(Supplier $supplier, CltLayup $layup): View
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);
        $this->authorize('view', $layup);

        $layers = $layup->layers()->paginate(20);

        return view('clt-layers.index', compact('supplier', 'layup', 'layers'));
    }

    public function create(Supplier $supplier, CltLayup $layup): View
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);
        $this->authorize('create', [CltLayer::class, $layup]);

        return view('clt-layers.create', compact('supplier', 'layup'));
    }

    public function store(StoreCltLayerRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);

        $layup->layers()->create($request->validated());

        if ($request->input('after_save') === 'add_another') {
            return redirect()
                ->route('suppliers.layups.layers.create', [$supplier, $layup])
                ->with('status', 'Layer was created. Add the next layer when ready.');
        }

        return redirect()
            ->route('suppliers.layups.layers.index', [$supplier, $layup])
            ->with('status', 'Layer was created.');
    }

    public function edit(Supplier $supplier, CltLayup $layup, CltLayer $layer): View
    {
        $this->ensureLayerBelongsToLayup($supplier, $layup, $layer);
        $this->authorize('update', $layer);

        return view('clt-layers.edit', compact('supplier', 'layup', 'layer'));
    }

    public function update(UpdateCltLayerRequest $request, Supplier $supplier, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $this->ensureLayerBelongsToLayup($supplier, $layup, $layer);

        $layer->update($request->validated());

        return redirect()
            ->route('suppliers.layups.layers.index', [$supplier, $layup])
            ->with('status', 'Layer was updated.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer): RedirectResponse
    {
        $this->ensureLayerBelongsToLayup($supplier, $layup, $layer);
        $this->authorize('delete', $layer);

        $layer->delete();

        return redirect()
            ->route('suppliers.layups.layers.index', [$supplier, $layup])
            ->with('status', 'Layer was deleted.');
    }

    private function ensureLayupBelongsToSupplier(Supplier $supplier, CltLayup $layup): void
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);
    }

    private function ensureLayerBelongsToLayup(Supplier $supplier, CltLayup $layup, CltLayer $layer): void
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);

        abort_unless($layer->layup_id === $layup->id, 404);
    }
}
