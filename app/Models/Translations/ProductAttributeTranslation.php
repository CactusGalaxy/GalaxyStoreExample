<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'locale',
        'product_attribute_id',
        'name',
    ];
}
