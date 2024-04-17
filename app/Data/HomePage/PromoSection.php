<?php

namespace App\Data\HomePage;

use App\Data\Image;
use App\Data\Localized;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class PromoSection extends Data
{
    /**
     * @param Collection<int, Image> $slider
     */
    public function __construct(
        public Image $mainImage,
        public Localized $title,
        public Localized $description,
        public Collection $slider,
    ) {
    }
}
