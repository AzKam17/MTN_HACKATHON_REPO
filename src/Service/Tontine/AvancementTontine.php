<?php

namespace App\Service\Tontine;

use App\Entity\Cotisation;
use App\Entity\Tontine;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AvancementTontine
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function __invoke(
        Tontine $tontine
    )
    {
        $compteur = $tontine->getCompteur() ?? 0;

        //Get all cotisations for this tontine equal to the compteur
        $cotisations = $this->em
            ->getRepository(Cotisation::class)
            ->findBy(['tontine' => $tontine, 'compteur' => $compteur])
        ;

        $users = [];
        foreach ($cotisations as $cotisation) {
            $users[] = $cotisation->getUser();
        }

        //Get all users for this tontine
        $userTontines = $tontine->getMembres();

        //Get the users that have not paid
        $usersNotPaid = [];
        foreach ($userTontines as $userTontine) {
            if (!in_array($userTontine->getUser(), $users)) {
                $usersNotPaid[] = $userTontine->getUser();
            }
        }

        //Percentage of users that have not paid
        $percentage = count($usersNotPaid) / count($userTontines) * 100;

        return [
            'pourcentage' => $percentage,
            'retard' => array_map(function (User $user){
                return $user->toArray();
            }, $usersNotPaid)
        ];
    }
}