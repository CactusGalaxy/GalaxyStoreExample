<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class BannerTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'locale',
        'banner_id',
        'title',
        'description',
    ];
}
