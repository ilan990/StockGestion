<?php

namespace App\Story;

use App\Factory\CategoryFactory;
use Zenstruck\Foundry\Story;

final class DefaultCategoriesStory extends Story
{
    public function build(): void
    {
        CategoryFactory::createMany(100);
    }
}
