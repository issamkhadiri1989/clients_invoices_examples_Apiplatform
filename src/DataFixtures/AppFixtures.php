<?php

namespace App\DataFixtures;

use App\Factory\ClientFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);

        ClientFactory::createMany(900, fn () => ['manager' => UserFactory::random()]);


        $manager->flush();
    }
}
