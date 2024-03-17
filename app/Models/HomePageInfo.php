<?php

namespace App\Models;

use App\Data\HomePage\HeroSection;
use App\Data\HomePage\PromoSection;
use App\Models\Contracts\Singular;
use App\Models\Traits\SingularModel;
use Illuminate\Database\Eloquent\Model;

class HomePageInfo extends Model implements Singular
{
    use SingularModel;

    protected $fillable = [
        'hero_section',
        'promo_section',
    ];

    protected $casts = [
        'hero_section' => HeroSection::class,
        'promo_section' => PromoSection::class,
    ];
}
