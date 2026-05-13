<?php

namespace App\Http\Requests;

use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCltLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('layer')) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var CltLayup $layup */
        $layup = $this->route('layup');
        /** @var CltLayer $layer */
        $layer = $this->route('layer');

        return [
            'layer_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('clt_layers', 'layer_order')
                    ->where('layup_id', $layup->id)
                    ->ignore($layer),
            ],
            'thickness' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'angle' => ['required', 'numeric'],
        ];
    }
}
