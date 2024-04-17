<?php

namespace App\Data\HomePage;

use App\Data\Image;
use App\Data\Localized;
use Spatie\LaravelData\Data;

class HeroSection extends Data
{
    public function __construct(
        public Image $image,
        public Localized $textLeft,
        public Localized $textRight,
        public Localized $quote,
    ) {
    }
}
