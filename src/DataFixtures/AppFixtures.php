<?php

namespace App\DataFixtures;

use App\Entity\Flight;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $flight = new Flight();
            $flight->setStatus('active');
            $manager->persist($flight);
        }
        $manager->flush();

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setName('test');
            $manager->persist($user);
        }
        $manager->flush();
    }
}
