<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CltLayup extends Model
{
    /** @use HasFactory<\Database\Factories\CltLayupFactory> */
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
    ];

    /**
     * @return BelongsTo<Supplier, CltLayup>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return HasMany<CltLayer>
     */
    public function layers(): HasMany
    {
        return $this->hasMany(CltLayer::class, 'layup_id')->orderBy('layer_order');
    }
}
