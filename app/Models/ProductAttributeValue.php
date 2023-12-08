<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'product_attribute_id',
        'attribute_value_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)
            ->with(['translations']);
    }

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class)
            ->with(['translations']);
    }

    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class)
            ->with(['translations']);
    }
}
