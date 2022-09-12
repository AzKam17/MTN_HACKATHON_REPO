<?php

namespace App\Tests\Service\Tontine;

use App\Entity\MontantTontine;
use App\Entity\PeriodiciteTontine;
use App\Service\CreateUser;
use App\Service\Tontine\CreateTontine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateTontineTest extends KernelTestCase
{
    public function testTontineCreation(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        $createUserService = static::getContainer()->get(CreateUser::class);
        $manager = static::getContainer()->get(EntityManagerInterface::class);
        $tontine = static::getContainer()->get(CreateTontine::class);

        $montantTontine = (new MontantTontine())->setValeur(1000);
        $periodicite = (new PeriodiciteTontine())->setValue('Mensuelle');
        //Create user
        $user = $createUserService(
            'username',
            'nom',
            'prenom',
            '+2250000000000',
            'password'
        );
        $manager->persist($user);
        $manager->persist($montantTontine);
        $manager->persist($periodicite);
        $manager->flush();

        //Create tontine
        $tontine = $tontine(
            'tontine',
            $montantTontine,
            $periodicite,
            $user,
            0,
            0,
        );
        $manager->persist($tontine);
        $manager->flush();

        $this->assertEquals('tontine', $tontine->getNom());
        $this->assertEquals(0, $tontine->getSolde());
        $this->assertEquals(1000, $tontine->getMontant()->getValeur());
        $this->assertEquals('Mensuelle', $tontine->getPeriodicite()->getValue());
        $this->assertEquals(0, $tontine->getCompteur());
        $this->assertEquals(0, $tontine->getSolde());
        $this->assertEquals($user, $tontine->getCreatedBy());
        $this->assertEquals($user->getId(), $tontine->getListeRetrait()->getMembres()[0]['id']);
        $this->assertEquals(1, $tontine->getListeRetrait()->getMembres()[0]['position']);
        $this->assertEquals(false, $tontine->getListeRetrait()->getMembres()[0]['isActive']);
        $this->assertEquals(true, $tontine->isIsActive());

        //Remove all
        $manager->remove($tontine);
        $manager->remove($user);
        $manager->remove($montantTontine);
        $manager->remove($periodicite);
        $manager->flush();
    }
}
