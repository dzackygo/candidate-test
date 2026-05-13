<?php

namespace App\Http\Controllers;

use App\Contracts\SupplierExportServiceInterface;
use App\Contracts\SupplierImportServiceInterface;
use App\Http\Requests\ImportSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SupplierImportExportController extends Controller
{
    public function export(Supplier $supplier, SupplierExportServiceInterface $exportService): JsonResponse
    {
        $this->authorize('view', $supplier);

        $filename = Str::slug($supplier->name).'-supplier-export.json';

        return response()
            ->json($exportService->export($supplier), 200, [
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ], JSON_PRETTY_PRINT);
    }

    public function import(
        ImportSupplierRequest $request,
        Supplier $supplier,
        SupplierImportServiceInterface $importService
    ): RedirectResponse {
        $payload = $this->decodePayload($request);
        $result = $importService->import($supplier, $payload);

        if ($result['status'] === 'conflicts') {
            session()->put("supplier_import.{$supplier->id}", $result);

            return redirect()
                ->route('suppliers.import-conflicts.show', $supplier)
                ->with('status', 'Import conflicts need review before changes are applied.');
        }

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', "Import completed for {$result['layup_count']} layups.");
    }

    /**
     * @return array<string, mixed>
     */
    private function decodePayload(ImportSupplierRequest $request): array
    {
        $contents = file_get_contents($request->file('import_file')->getRealPath());
        $payload = json_decode($contents ?: '', true);

        if (! is_array($payload)) {
            throw ValidationException::withMessages([
                'import_file' => 'The import file must contain valid JSON.',
            ]);
        }

        return $payload;
    }
}
