<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUser
{
    public function __construct(
        private UserRepository $userRepository,
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
        //Check if User already exists BY username
        $user = $this->userRepository->findOneBy(['username' => $username]);
        if ($user) {
            throw new \Exception('User already exists');
        }
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