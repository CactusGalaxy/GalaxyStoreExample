<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class AttributeValueTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'locale',
        'attribute_value_id',
        'name',
    ];
}
