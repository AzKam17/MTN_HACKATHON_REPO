<?php

namespace App\DataFixtures;

use App\Service\CreateUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServicesAccountFixtures extends Fixture
{

    public function __construct(
        private CreateUser $createUser,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $user = ($this->createUser)
        (...[
            'username' => 'Admin',
            'nom' => 'admin',
            'prenom' => 'admin',
            'tel' => '0779136356',
            'password' => 'esatic',
            'solde' => 1000000000000000000000000000000000000,
        ]);

        $user2 = ($this->createUser)
        (...[
            'username' => 'Admin2',
            'nom' => 'Mariko',
            'prenom' => 'Oudou',
            'tel' => '0757351113',
            'password' => 'esatic',
            'solde' => 100,
        ]);

        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }
}
