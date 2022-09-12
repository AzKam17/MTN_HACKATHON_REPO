<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;

class CreateUserTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateAccountWithoutSolde(): void
    {

        //Load EntityManagerInterface
        $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);

        //Load Create User Service
        $createUser = new \App\Service\CreateUser(
            $this->createMock(\Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface::class),
            $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class),
        );

        //Create User
        $user = $createUser(
            'username',
            'nom',
            'prenom',
            '+2250000000000',
            'password'
        );

        //Assert User
        $this->assertInstanceOf(\App\Entity\User::class, $user);

        //Assert User Username
        $this->assertEquals('+2250000000000', $user->getUsername());

        //Assert User Nom
        $this->assertEquals('nom', $user->getNom());

        //Assert User Prenom
        $this->assertEquals('prenom', $user->getPrenom());

        //Assert User Tel
        $this->assertEquals('+2250000000000', $user->getTel());

        //Assert User Solde
        $this->assertEquals(0, $user->getSolde());
    }

    /**
     * @throws \Exception
     */
    public function testCreateAccountWithNullSolde(): void
    {
        //Load EntityManagerInterface
        $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);

        //Load Create User Service
        $createUser = new \App\Service\CreateUser(
            $this->createMock(\Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface::class),
            $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class),
        );

        //Create User
        $user = $createUser(
            'username',
            'nom',
            'prenom',
            '+2250000000000',
            'password',
            0
        );

        //Assert User
        $this->assertInstanceOf(\App\Entity\User::class, $user);

        //Assert User Username
        $this->assertEquals('+2250000000000', $user->getUsername());

        //Assert User Nom
        $this->assertEquals('nom', $user->getNom());

        //Assert User Prenom
        $this->assertEquals('prenom', $user->getPrenom());

        //Assert User Tel
        $this->assertEquals('+2250000000000', $user->getTel());

        //Assert User Solde
        $this->assertEquals(0, $user->getSolde());
    }


    /**
     * @throws \Exception
     */
    public function testCreateAccountWithNonNullSolde(): void
    {
        //Load EntityManagerInterface
        $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);

        //Load Create User Service
        $createUser = new \App\Service\CreateUser(
            $this->createMock(\Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface::class),
            $this->createMock(\Symfony\Component\Validator\Validator\ValidatorInterface::class),
        );

        //Create User
        $user = $createUser(
            'username',
            'nom',
            'prenom',
            '+2250000000000',
            'password',
            9999999999999
        );

        //Assert User
        $this->assertInstanceOf(\App\Entity\User::class, $user);

        //Assert User Username
        $this->assertEquals('+2250000000000', $user->getUsername());

        //Assert User Nom
        $this->assertEquals('nom', $user->getNom());

        //Assert User Prenom
        $this->assertEquals('prenom', $user->getPrenom());

        //Assert User Tel
        $this->assertEquals('+2250000000000', $user->getTel());

        //Assert User Solde
        $this->assertEquals(9999999999999, $user->getSolde());

        //Remove User
        $entityManager->remove($user);
        $entityManager->flush();
    }
}
