<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Contracts\Visible;
use App\Models\Traits\HasMediaAttributes;
use App\Models\Traits\VisibleTrait;
use App\Models\Traits\WithTranslationsTrait;
use App\Models\Translations\BannerTranslation;
use Astrotomic\Translatable\Translatable;
use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @method BannerTranslation translate(?string $locale = null, bool $withFallback = false)
 * @mixin BannerTranslation
 */
class Banner extends Model implements Sortable, Visible
{
    use HasFactory;
    use Translatable;
    use WithTranslationsTrait;
    use HasMediaAttributes;
    use SortableTrait;
    use VisibleTrait;

    public $translationModel = BannerTranslation::class;

    protected array $translatedAttributes = [
        'title',
        'description',
    ];

    protected $fillable = [
        'position',
        'status',
        'image',
    ];

    protected $casts = [
        'status' => 'boolean',
        'position' => 'integer',
    ];

    protected static function newFactory(): BannerFactory
    {
        return BannerFactory::new();
    }
}
