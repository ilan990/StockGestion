<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use App\Story\DefaultBottlesStory;
use App\Story\DefaultOrganizationStory;
use App\Story\DefaultStockMovementStory;
use App\Story\DefaultUserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultOrganizationStory::load();
        DefaultUserStory::load();
        DefaultOrganizationStory::load();
        DefaultBottlesStory::load();
        DefaultStockMovementStory::load();
        $manager->flush();
    }
}
