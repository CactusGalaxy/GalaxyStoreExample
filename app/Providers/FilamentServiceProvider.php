<?php

namespace App\Providers;

use App\Models\Traits\WithTranslationsTrait;
use Astrotomic\Translatable\Translatable;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Facades\FilamentView;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureColumns();
        $this->configureInputs();

        Table::macro('loadTranslations', function (): Table {
            /* @var Table $this */
            return $this->modifyQueryUsing(function (Builder $query) {
                $query->with('translations');
            });
        });

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };

        FilamentView::registerRenderHook('panels::auth.login.form.after', fn () => view('login-link'));
    }

    private function configureColumns(): void
    {
        Tables\Columns\Column::configureUsing(function (Tables\Columns\Column $column): void {
            $column->label(fn (Tables\Columns\Column $column) => $this->getAttributeTranslation(
                str(class_basename($column->getTable()->getModel()))->kebab()->plural(),
                $column->getName()
            ));
        });

        Tables\Columns\ToggleColumn::configureUsing(function (Tables\Columns\ToggleColumn $column): void {
            if (Str::contains($column->getName(), 'status')) {
                $column
                    ->afterStateUpdated(function () {
                        Notification::make()
                            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                            ->success()
                            ->send();
                    })
                    ->sortable();
            }
        });

        Tables\Columns\TextInputColumn::configureUsing(function (Tables\Columns\TextInputColumn $column): void {
            if (Str::contains($column->getName(), 'position')) {
                $column
                    ->afterStateUpdated(function () {
                        Notification::make()
                            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                            ->success()
                            ->send();
                    })
                    ->sortable();
            }
        });

        Tables\Columns\TextColumn::configureUsing(function (Tables\Columns\TextColumn $column): void {
            if (Str::match('@^translations?\.(\w+)$@', $column->getName())) {
                $column
                    ->searchable(query: function (Builder $query, string $search) use ($column): Builder {
                        $columnName = Str::after($column->getName(), '.');
                        if ($query->hasNamedScope('whereTranslationLike')) {
                            /* @var WithTranslationsTrait|Translatable $query */
                            return $query->whereTranslationLike($columnName, "%{$search}%");
                        }

                        return $query->where($columnName, 'like', "%{$search}%");
                    });
                // todo - fix id is ambiguous
                // ->sortable(query: function (Builder $query, string $direction) use ($column): Builder {
                //     $columnName = Str::after($column->getName(), '.');
                //     if ($query->hasNamedScope('orderByTranslation')) {
                //         /* @var WithTranslationsTrait|Translatable $query */
                //         return $query->orderByTranslation($columnName, $direction);
                //     }
                //
                //     return $query->orderBy($columnName, $direction);
                // })
            }
        });
    }

    private function configureInputs(): void
    {
        Forms\Components\Placeholder::configureUsing(function (Forms\Components\Placeholder $placeholder): void {
            $placeholder->label(fn () => $this->getAttributeTranslation(
                str(class_basename($placeholder->getModelInstance()))->kebab()->plural(),
                $placeholder->getName(),
            ));
        });
        Forms\Components\Field::configureUsing(function (Forms\Components\Field $field): void {
            $field->label(fn () => $this->getAttributeTranslation(
                str(class_basename($field->getModelInstance()))->kebab()->plural(),
                $field->getName(),
            ));
        });
    }

    private function getAttributeTranslation(string $resourceSlug, string $name): ?string
    {
        if (str($name)->before('.')->contains(config('translatable.locales'))) {
            $name = str($name)->after('.')->value();
        }

        if (str($name)->before('.')->contains('translation')) {
            $name = str($name)->after('.')->value();
        }

        return match (true) {
            trans()->has($key = "filament/resources/{$resourceSlug}.attributes.{$name}") => __($key),
            trans()->has($key = "admin_labels.attributes.{$name}") => __($key),
            default => str($name)
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst()
                ->value(),
        };
    }
}
