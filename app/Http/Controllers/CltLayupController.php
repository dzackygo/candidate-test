<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCltLayupRequest;
use App\Http\Requests\UpdateCltLayupRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CltLayupController extends Controller
{
    public function index(Supplier $supplier): View
    {
        $this->authorize('view', $supplier);

        $layups = $supplier->layups()
            ->withCount('layers')
            ->paginate(10);

        return view('clt-layups.index', compact('supplier', 'layups'));
    }

    public function create(Supplier $supplier): View
    {
        $this->authorize('create', [CltLayup::class, $supplier]);

        return view('clt-layups.create', compact('supplier'));
    }

    public function store(StoreCltLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $layup = $supplier->layups()->create($request->validated());

        return redirect()
            ->route('suppliers.layups.show', [$supplier, $layup])
            ->with('status', "{$layup->name} was created. Add layers to complete the stack.");
    }

    public function show(Supplier $supplier, CltLayup $layup): View
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);
        $this->authorize('view', $layup);

        $layup->load('layers');

        return view('clt-layups.show', compact('supplier', 'layup'));
    }

    public function edit(Supplier $supplier, CltLayup $layup): View
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);
        $this->authorize('update', $layup);

        return view('clt-layups.edit', compact('supplier', 'layup'));
    }

    public function update(UpdateCltLayupRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);

        $layup->update($request->validated());

        return redirect()
            ->route('suppliers.layups.show', [$supplier, $layup])
            ->with('status', "{$layup->name} was updated.");
    }

    public function destroy(Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);
        $this->authorize('delete', $layup);

        $layup->delete();

        return redirect()
            ->route('suppliers.layups.index', $supplier)
            ->with('status', 'Layup was deleted.');
    }

    private function ensureLayupBelongsToSupplier(Supplier $supplier, CltLayup $layup): void
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);
    }
}
