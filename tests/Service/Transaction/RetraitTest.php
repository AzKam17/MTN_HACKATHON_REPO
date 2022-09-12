<?php

namespace App\Tests\Service\Transaction;

use App\Entity\User;
use App\Service\CreateUser;
use App\Service\Transaction\DepotCompte;
use App\Service\Transaction\Retrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RetraitTest extends KernelTestCase
{
    public function testRetrait(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        $createUserService = static::getContainer()->get(CreateUser::class);
        $retraitService = static::getContainer()->get(Retrait::class);
        $manager = static::getContainer()->get(EntityManagerInterface::class);

        $admin = $manager
            ->getRepository(User::class)
            ->findOneBy(['slug' => 'admin-admin-0779136356']);

        //Create user provider with 1000 as solde
        $provider = $createUserService(
            'provider',
            'nom',
            'prenom',
            '+2250000000001',
            'password',
            1000
        );

        //Create user client with 1000 as solde
        $client = $createUserService(
            'client',
            'nom',
            'prenom',
            '+2250000000002',
            'password',
            2000
        );

        $manager->persist($provider);
        $manager->persist($client);
        $manager->flush();

        $adminPrevSolde = $admin->getSolde();
        $retraitService($provider, $client, 1000);

        $this->assertEquals(1000 + 1000 + (1000 * 0.01 * 0.5), $provider->getSolde());
        $this->assertEquals(2000 - 1000 - (1000 * 0.01), $client->getSolde());
        $this->assertEquals($adminPrevSolde + (1000 * 0.01 * 0.5), $admin->getSolde());

        $admin->setSolde($adminPrevSolde);
        //Remove user
        $manager->remove($provider);
        $manager->remove($client);
        $manager->flush();
    }

    public function testNotEnoughMoney(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        $createUserService = static::getContainer()->get(CreateUser::class);
        $retraitService = static::getContainer()->get(Retrait::class);
        $manager = static::getContainer()->get(EntityManagerInterface::class);

        //Create user provider with 1000 as solde
        $provider = $createUserService(
            'provider',
            'nom',
            'prenom',
            '+2250000000001',
            'password',
            1000
        );

        //Create user client with 1000 as solde
        $client = $createUserService(
            'client',
            'nom',
            'prenom',
            '+2250000000002',
            'password',
            0
        );

        $manager->persist($provider);
        $manager->persist($client);
        $manager->flush();

        //Check if error is thrown
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Sender has not enough money');

        $retraitService($provider, $client, 10000);

        //Remove user
        $manager->remove($provider);
        $manager->remove($client);
        $manager->flush();
    }
}
