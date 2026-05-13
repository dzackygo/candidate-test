<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResolveSupplierImportConflictsRequest;
use App\Models\Supplier;
use App\Services\SupplierImportConflictService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierImportConflictController extends Controller
{
    public function show(Supplier $supplier): View|RedirectResponse
    {
        $this->authorize('update', $supplier);

        $draft = session($this->sessionKey($supplier));

        if (! is_array($draft)) {
            return redirect()
                ->route('suppliers.show', $supplier)
                ->with('status', 'There are no pending import conflicts for this supplier.');
        }

        return view('supplier-import-conflicts.show', [
            'supplier' => $supplier,
            'conflicts' => $draft['conflicts'],
        ]);
    }

    public function resolve(
        ResolveSupplierImportConflictsRequest $request,
        Supplier $supplier,
        SupplierImportConflictService $conflictService
    ): RedirectResponse {
        $draft = session($this->sessionKey($supplier));

        if (! is_array($draft)) {
            return redirect()
                ->route('suppliers.show', $supplier)
                ->with('status', 'There are no pending import conflicts for this supplier.');
        }

        $result = $conflictService->resolve($supplier, $draft, $request->validated('decisions'));
        session()->forget($this->sessionKey($supplier));

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', "Import conflicts resolved. {$result['applied_layers']} incoming layers were applied.");
    }

    private function sessionKey(Supplier $supplier): string
    {
        return "supplier_import.{$supplier->id}";
    }
}
