<?php

namespace App\Service\Tontine;

use App\Entity\Tontine;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RemoveMember
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function __invoke(
        User $userToRemove,
        Tontine $tontine,
    )
    {
        //If Tontine is not active, we can't remove a member
        if (!$tontine->isIsActive()) {
            throw new \Exception('Tontine is not active');
        }

        //Check if there is an object UserTontine with the user and the tontine
        $userTontine = $tontine->getMembres()->filter(function ($userTontine) use ($userToRemove) {
            return $userTontine->getUser() === $userToRemove;
        })->first();

        //If user is not a member, we can't remove him
        if (!$userTontine) {
            throw new \Exception('User is not a member');
        }

        //If user is a member, check if he is removed
        if ($userTontine->isIsRemoved()) {
            throw new \Exception('User is already removed');
        }

        //If user is a member and not removed, remove him
        if (!$userTontine->isIsRemoved()) {
            $userTontine->setIsRemoved(true);
            $this->em->persist($userTontine);
            $this->em->persist($tontine);
        }
        $this->em->flush();
    }
}