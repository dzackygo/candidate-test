<?php

namespace App\Contracts;

use App\Models\Supplier;

interface SupplierImportServiceInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function import(Supplier $supplier, array $payload): array;
}
