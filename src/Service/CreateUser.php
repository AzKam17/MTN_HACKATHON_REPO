<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUser
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        string $username,
        string $nom,
        string $prenom,
        string $tel,
        string $password,
        float $solde = 0,
    ) : User
    {
        $user = new User();
        $user->setUsername($tel);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setTel($tel);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $password));

        $user->setSolde($solde ?? 0);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }

        return $user;
    }
}