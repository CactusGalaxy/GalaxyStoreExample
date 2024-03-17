<?php

namespace App\Data\HomePage;

use App\Data\Image;
use App\Data\Localised;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class PromoSection extends Data
{
    /**
     * @param Collection<int, Image> $slider
     */
    public function __construct(
        public Image $mainImage,
        public Localised $title,
        public Localised $description,
        public Collection $slider,
    ) {
    }
}
