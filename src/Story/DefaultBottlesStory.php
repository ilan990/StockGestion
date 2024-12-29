<?php

namespace App\Story;

use App\Factory\BottleFactory;
use Zenstruck\Foundry\Story;

final class DefaultBottlesStory extends Story
{
    public function build(): void
    {
        BottleFactory::createMany(100);
    }
}
