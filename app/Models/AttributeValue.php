<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Contracts\Visible;
use App\Models\Traits\VisibleTrait;
use App\Models\Traits\WithTranslationsTrait;
use App\Models\Translations\AttributeValueTranslation;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @method AttributeValueTranslation translate(?string $locale = null, bool $withFallback = false)
 * @mixin AttributeValueTranslation
 */
class AttributeValue extends Model implements Sortable, Visible
{
    use Translatable;
    use WithTranslationsTrait;
    use SortableTrait;
    use VisibleTrait;

    public $translationModel = AttributeValueTranslation::class;

    protected array $translatedAttributes = [
        'name',
        'value',
    ];

    protected $fillable = [
        'product_attribute_id',
        'position',
        'status',
    ];

    protected $casts = [
        'position' => 'integer',
        'status' => 'bool',
    ];

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
            ->where('status', true)
            ->withPivot([
                'product_id',
                'product_attribute_id',
                'attribute_value_id',
            ]);
    }
}
