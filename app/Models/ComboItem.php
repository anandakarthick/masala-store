<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComboItem extends Model
{
    protected $fillable = [
        'combo_product_id',
        'included_product_id',
        'item_name',
        'item_quantity',
        'item_description',
        'sort_order',
    ];

    /**
     * The combo/pack product this item belongs to
     */
    public function comboProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'combo_product_id');
    }

    /**
     * The actual product that's included (optional - for linking)
     */
    public function includedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'included_product_id');
    }
}
