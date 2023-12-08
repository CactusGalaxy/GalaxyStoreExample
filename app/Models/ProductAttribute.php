<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductAttribute\DisplayType;
use App\Models\Contracts\Visible;
use App\Models\Traits\VisibleTrait;
use App\Models\Traits\WithTranslationsTrait;
use App\Models\Translations\ProductAttributeTranslation;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @method ProductAttributeTranslation translate(?string $locale = null, bool $withFallback = false)
 * @mixin ProductAttributeTranslation
 */
class ProductAttribute extends Model implements Sortable, Visible
{
    use Translatable;
    use WithTranslationsTrait;
    use SortableTrait;
    use VisibleTrait;

    public $translationModel = ProductAttributeTranslation::class;

    protected array $translatedAttributes = [
        'name',
    ];

    protected $attributes = [
        'display_type_in_card' => DisplayType::TEXT,
        'position' => 1,
    ];

    protected $fillable = [
        'position',
        'status',
        'key',
        'display_type_in_card',
    ];

    protected $casts = [
        'display_type_in_card' => DisplayType::class,
    ];

    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class)
            ->with(['translations'])
            ->ordered();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
            ->withPivot([
                'product_id',
                'product_attribute_id',
                'attribute_value_id',
            ]);
    }
}
