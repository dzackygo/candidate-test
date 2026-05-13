<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CltLayer extends Model
{
    /** @use HasFactory<\Database\Factories\CltLayerFactory> */
    use HasFactory;

    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
    ];

    protected function casts(): array
    {
        return [
            'layer_order' => 'integer',
            'thickness' => 'decimal:3',
            'width' => 'decimal:3',
            'angle' => 'decimal:3',
        ];
    }

    /**
     * @return BelongsTo<CltLayup, CltLayer>
     */
    public function layup(): BelongsTo
    {
        return $this->belongsTo(CltLayup::class, 'layup_id');
    }
}
