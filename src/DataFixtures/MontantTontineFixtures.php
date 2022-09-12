<?php

namespace App\DataFixtures;

use App\Entity\MontantTontine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MontantTontineFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $montant1 = new MontantTontine();
        $montant1->setValeur(1000);
        $manager->persist($montant1);

        $montant2 = new MontantTontine();
        $montant2->setValeur(2000);
        $manager->persist($montant2);

        $montant3 = new MontantTontine();
        $montant3->setValeur(5000);
        $manager->persist($montant3);

        $manager->flush();
    }
}
