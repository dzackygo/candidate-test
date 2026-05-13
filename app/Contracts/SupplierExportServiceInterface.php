<?php

namespace App\Contracts;

use App\Models\Supplier;

interface SupplierExportServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function export(Supplier $supplier): array;
}
