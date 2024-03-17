<?php

namespace App\Data\HomePage;

use App\Data\Image;
use App\Data\Localised;
use Spatie\LaravelData\Data;

class HeroSection extends Data
{
    public function __construct(
        public Image $image,
        public Localised $textLeft,
        public Localised $textRight,
        public Localised $quote,
    ) {
    }
}
