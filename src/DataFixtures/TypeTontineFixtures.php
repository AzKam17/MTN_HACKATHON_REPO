<?php

namespace App\DataFixtures;

use App\Entity\TypeTontine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeTontineFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $typeTontine1 = new TypeTontine();
        $typeTontine1->setValue('Tontine avec intérêt');
        $manager->persist($typeTontine1);

        $typeTontine4 = new TypeTontine();
        $typeTontine4->setValue('Tontine ordinaire');
        $manager->persist($typeTontine4);

        $manager->flush();
    }
}
