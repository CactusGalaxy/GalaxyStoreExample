<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Contracts\Visible;
use App\Models\Traits\HasMediaAttributes;
use App\Models\Traits\VisibleTrait;
use App\Models\Traits\WithTranslationsTrait;
use App\Models\Translations\CategoryTranslation;
use Astrotomic\Translatable\Translatable;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @method CategoryTranslation translate(?string $locale = null, bool $withFallback = false)
 * @mixin CategoryTranslation
 */
class Category extends Model implements Sortable, Visible
{
    use HasFactory;
    use Translatable;
    use WithTranslationsTrait;
    use HasMediaAttributes;
    use SortableTrait;
    use VisibleTrait;

    public $translationModel = CategoryTranslation::class;

    protected array $translatedAttributes = [
        'title',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $fillable = [
        'slug',
        'status',
        'position',
        'image',
    ];

    protected $casts = [
        'status' => 'boolean',
        'position' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        if (!request()->routeIs('web.*')) {
            return parent::getRouteKeyName();
        }

        return 'id';
    }

    public function getRouteKey()
    {
        if (!request()->routeIs('web.*')) {
            return parent::getRouteKey();
        }

        return Str::slug($this->title) . '-' . $this->getAttribute($this->getRouteKeyName());
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return parent::resolveRouteBindingQuery($query, $value, $field)
            ->when(request()->routeIs('web.*'))
            ->visible();
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if (!request()->routeIs('web.*')) {
            return parent::resolveRouteBinding($value, $field);
        }

        $id = last(explode('-', $value));
        $model = parent::resolveRouteBinding($id, $field);

        if (!$model || $model->getRouteKey() === $value) {
            return $model;
        }

        throw new HttpResponseException(
            redirect()->route(request()->route()->getName(), $model)
        );
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
