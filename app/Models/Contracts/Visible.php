<?php

declare(strict_types=1);

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Visible
{
    public function isVisible(): bool;

    public function scopeVisible(Builder $query): Builder;

    public function scopeHidden(Builder $query): Builder;
}
