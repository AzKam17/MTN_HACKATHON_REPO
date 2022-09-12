<?php

namespace App\DataFixtures;

use App\Entity\PeriodiciteTontine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PeriodiciteTontineFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $periodicite1 = new PeriodiciteTontine();
        $periodicite1->setValue("Hebdomadaire");
        $manager->persist($periodicite1);

        $periodicite2 = new PeriodiciteTontine();
        $periodicite2->setValue("Mensuelle");
        $manager->persist($periodicite2);

        $periodicite3 = new PeriodiciteTontine();
        $periodicite3->setValue("Trimestrielle");
        $manager->persist($periodicite3);

        $manager->flush();
    }
}
