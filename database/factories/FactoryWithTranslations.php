<?php

declare(strict_types=1);

namespace Database\Factories;

use Astrotomic\Translatable\Translatable;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @mixin Factory
 */
trait FactoryWithTranslations
{
    public function configure(): static
    {
        return $this->extendsConfigure();
    }

    public function extendsConfigure(): static
    {
        return $this
            ->translate()
            ->when($this->withPosition(), function (self $factory) {
                $positionColumns = collect($this->getPositionColumns())->flip();

                return $factory->sequence(
                    fn (Sequence $sequence) => $positionColumns->map(fn () => $sequence->index + 1)->toArray()
                );
            })
            ->when($this->withVisibleState(), function (self $factory) {
                return $factory->visibleState();
            });
    }

    public function translate(): static
    {
        return $this->afterMaking(function (Model $model) {
            if (!$this->isTranslatable($model)) {
                return $model;
            }

            $title = $this->getTitleTranslation();

            if (in_array('slug', $model->getFillable())) {
                $model->fill(['slug' => Str::slug($title)]);
            }

            /** @var Translatable|Model $model */
            $translatableModel = $model->getTranslationModelName();
            $translatableModel = new $translatableModel();

            $model->fill(
                $this->getTranslatedAttributes([
                    'name' => $this->getNameTranslation(),
                    'title' => $title,
                    'content' => $this->getDescriptionTranslation(),
                    'description' => $this->getDescriptionTranslation(),
                    'short_description' => $this->getDescriptionTranslation(1),
                    'meta_title' => 'meta: ' . $title,
                    'meta_description' => 'meta: ' . $this->getDescriptionTranslation(1),
                ], $translatableModel->getFillable())
            );

            return $model;
        });
    }

    public function translateMetaTitle(string $title = null): static
    {
        return $this->translateKey('meta_title', $title);
    }

    /**
     * @param string $key
     * @param Closure(string $locale, string $key): string|string|null $value
     * @return static
     */
    public function translateKey(string $key, Closure|string $value = null):static
    {
        return $this->afterMaking(function (Model $model) use ($key, $value) {
            /** @var Translatable|Model $model */
            $model->fill(
                $this->getTranslatedAttributes([
                    $key => $value ?: ($model->title ?? $this->getTitleTranslation()),
                ])
            );

            return $model;
        });
    }

    /**
     * @param string[]|Closure[] $keyValues
     * @param array|null $filterKeys
     * @return array
     */
    protected function getTranslatedAttributes(array $keyValues, array $filterKeys = null): array
    {
        $translations = collect();

        foreach (config('translatable.locales') as $locale) {
            $translations[$locale] = collect($keyValues)
                ->mapWithKeys(function (string|Closure $value, $key) use ($locale) {
                    $result = value($value, $locale, $key);

                    if (!is_string($result)) {
                        return [
                            $key => $result,
                        ];
                    }

                    return [
                        $key => $result . ' ' . $locale,
                    ];
                })
                ->when(!empty($filterKeys), fn (Collection $collection) => $collection->only($filterKeys));
        }

        return $translations->toArray();
    }

    protected function getTitleTranslation(int $words = 6): string
    {
        return $this->faker->words($words, true);
    }

    protected function getNameTranslation(int $words = 3): string
    {
        return $this->faker->words($words, true);
    }

    protected function getDescriptionTranslation(int $nb = 3): string
    {
        return $this->faker->paragraphs($nb, true);
    }

    protected function isTranslatable(Model $model): bool
    {
        return in_array(Translatable::class, array_keys(class_uses($model)));
    }

    protected function hiddenState(): static
    {
        return $this->state(
            collect($this->getVisibleStatusColumns())
                ->flip()
                ->map(fn () => false)
                ->toArray()
        );
    }

    protected function visibleState(): static
    {
        return $this->state(
            collect($this->getVisibleStatusColumns())
                ->flip()
                ->map(fn () => true)
                ->toArray()
        );
    }

    /**
     * @return array<int, string>
     */
    protected function getVisibleStatusColumns(): array
    {
        return [
            'status',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getPositionColumns(): array
    {
        return [
            'position',
        ];
    }

    protected function withVisibleState(): bool
    {
        return in_array('status', $this->newModel()->getFillable());
    }

    protected function withPosition(): bool
    {
        return in_array('position', $this->newModel()->getFillable());
    }
}
