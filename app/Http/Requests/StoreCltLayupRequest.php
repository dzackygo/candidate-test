<?php

namespace App\Http\Requests;

use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCltLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [CltLayup::class, $this->route('supplier')]) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clt_layups', 'name')->where('supplier_id', $supplier->id),
            ],
        ];
    }
}
