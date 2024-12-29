<?php

namespace App\Story;

use Zenstruck\Foundry\Story;
use App\Factory\StockMovementFactory;

final class DefaultStockMovementStory extends Story
{
    public function build(): void
    {
        StockMovementFactory::createMany(100);
    }
}
