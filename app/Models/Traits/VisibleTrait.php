<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait VisibleTrait
{
    protected function getVisibilityColumn(): string
    {
        return 'status';
    }

    public function isVisible(): bool
    {
        return (bool)$this->getAttribute($this->getVisibilityColumn());
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.' . $this->getVisibilityColumn(), true);
    }

    public function scopeHidden(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.' . $this->getVisibilityColumn(), false);
    }
}
