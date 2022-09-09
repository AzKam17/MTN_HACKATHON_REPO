<?php

namespace App\DataFixtures;

use App\Service\CreateUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
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
        ($this->createUser)
        (...[
            'username' => 'Admin',
            'nom' => 'admin',
            'prenom' => 'admin',
            'tel' => '0779136356',
            'password' => 'esatic',
            'solde' => 1000000,
        ]);
    }
}
