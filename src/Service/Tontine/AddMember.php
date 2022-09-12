<?php

namespace App\Service\Tontine;

use App\Entity\Tontine;
use App\Entity\User;
use App\Entity\UserTontine;
use Doctrine\ORM\EntityManagerInterface;

class AddMember
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function __invoke(
        User $userToAdd,
        Tontine $tontine,
    )
    {
        //If tontine compteur is not 0, we can't add a member
        if ($tontine->getCompteur() !== 0) {
            throw new \Exception('Tontine is already started');
        }

        //If Tontine is not active, we can't add a member
        if (!$tontine->isIsActive()) {
            throw new \Exception('Tontine is not active');
        }

        //Check if there is an object UserTontine with the user and the tontine
        $userTontine = $tontine->getMembres()->filter(function ($userTontine) use ($userToAdd) {
            return $userTontine->getUser() === $userToAdd;
        })->first();

        //If user is already a member, we can't add him
        if ($userTontine && !$userTontine->isIsRemoved()) {
            throw new \Exception('User is already a member');
        }

        //If there is no object UserTontine, create one
        if (!$userTontine) {
            $userTontine = new UserTontine();
            $userTontine->setUser($userToAdd);
            $userTontine->setTontine($tontine);
            $userTontine->setIsRemoved(false);
            $this->em->persist($userTontine);
            $tontine->addMembre($userTontine);
            //Update ListRetrait
            $listRetrait = $tontine->getListeRetrait();
            $membresListRetrait = $listRetrait->getMembres();
            $membresListRetrait[] = [
                'position' => count($membresListRetrait) + 1,
                'id' => $userToAdd->getId(),
                'isActive' => false,
            ];
            $listRetrait->setMembres($membresListRetrait);
            $this->em->persist($listRetrait);
            $this->em->persist($tontine);
        }

        //If there is an object UserTontine, check if it is removed
        if ($userTontine->isIsRemoved()) {
            $userTontine->setIsRemoved(false);
            $this->em->persist($userTontine);
            //Update ListRetrait
            $listRetrait = $tontine->getListeRetrait();
            $membresListRetrait = $listRetrait->getMembres();
            $membresListRetrait = array_map(function ($membre) use ($userToAdd) {
                if ($membre['id'] === $userToAdd->getId()) {
                    $membre['isActive'] = true;
                }
                return $membre;
            }, $membresListRetrait);
            $listRetrait->setMembres($membresListRetrait);
            $this->em->persist($listRetrait);
            $this->em->persist($tontine);
        }

        $this->em->flush();
    }
}