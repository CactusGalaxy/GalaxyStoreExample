<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Contracts\Visible;
use App\Models\Traits\HasMediaAttributes;
use App\Models\Traits\VisibleTrait;
use App\Models\Traits\WithTranslationsTrait;
use App\Models\Translations\ProductTranslation;
use Astrotomic\Translatable\Translatable;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method ProductTranslation translate(?string $locale = null, bool $withFallback = false)
 * @mixin ProductTranslation
 */
class Product extends Model implements Visible
{
    use HasFactory;
    use Translatable;
    use WithTranslationsTrait;
    use VisibleTrait;
    use HasMediaAttributes;

    public $translationModel = ProductTranslation::class;

    protected array $translatedAttributes = [
        'title',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'characteristics',
    ];

    protected $fillable = [
        'category_id',
        'status',
        'slug',
        'sku',
        'quantity',
        'price',
        'image',
        'sliders',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sliders' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productAttributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function scopeWhereHasVisibleCategory(Builder $query): Builder
    {
        return $query->withWhereHas(
            'category',
            fn ($category) => /* @var Category $category */ $category
                ->visible()
        );
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
