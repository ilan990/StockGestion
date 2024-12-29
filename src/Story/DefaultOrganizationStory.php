<?php

namespace App\Story;

use App\Factory\OrganizationFactory;
use Zenstruck\Foundry\Story;

final class DefaultOrganizationStory extends Story
{
    public function build(): void
    {
        OrganizationFactory::createMany(100);
    }
}
