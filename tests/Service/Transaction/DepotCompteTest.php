<?php

namespace App\Tests\Service\Transaction;

use App\Service\CreateUser;
use App\Service\Transaction\DepotCompte;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DepotCompteTest extends KernelTestCase
{
    /**
     * @throws \Exception
     */
    public function testDepotCompte(): void
    {
        $kernel = self::bootKernel();

        $createUserService = static::getContainer()->get(CreateUser::class);
        $depotCompteService = static::getContainer()->get(DepotCompte::class);
        $manager = static::getContainer()->get(EntityManagerInterface::class);

        $user = $createUserService(
            'username',
            'nom',
            'prenom',
            '+2250000000000',
            'password'
        );
        $manager->persist($user);
        $manager->flush();

        $depotCompteService($user, 1000);

        $this->assertEquals(1000, $user->getSolde());
        //Remove user
        $manager->remove($user);
        $manager->flush();
    }
}
