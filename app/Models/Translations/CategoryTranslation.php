<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'locale',
        'category_id',
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];
}
