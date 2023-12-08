<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'locale',
        'product_id',
        'title',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'characteristics',
    ];

    protected $casts = [
        'characteristics' => 'array',
    ];
}
