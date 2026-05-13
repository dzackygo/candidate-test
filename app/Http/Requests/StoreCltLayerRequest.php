<?php

namespace App\Http\Requests;

use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCltLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [CltLayer::class, $this->route('layup')]) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var CltLayup $layup */
        $layup = $this->route('layup');

        return [
            'layer_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('clt_layers', 'layer_order')->where('layup_id', $layup->id),
            ],
            'thickness' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'angle' => ['required', 'numeric'],
        ];
    }
}
