<?php

namespace App\Service\Tontine;

use App\Entity\Cotisation;
use App\Entity\Tontine;
use App\Entity\User;
use App\Entity\UserTontine;
use Doctrine\ORM\EntityManagerInterface;

class CheckStateUserCotisation
{
    public function __construct(
        private EntityManagerInterface $manager
    )
    {
    }

    public function __invoke(
        User $user,
        Tontine $tontine
    )
    {
        if ($tontine->getCompteur() <= 0) {
            throw new \Exception('La tontine n\'a pas encore commencé');
        }

        //Check if user is member of tontine
        $userTontine = $this->manager->getRepository(UserTontine::class)->findOneBy([
            'user' => $user,
            'tontine' => $tontine
        ]);

        //if user is not member of tontine, throw exception
        if (!$userTontine) {
            throw new \Exception('Vous n\'êtes pas membre de cette tontine');
        }

        //Find last cotisation of user
        $lastCotisation = $this->manager->getRepository(Cotisation::class)->findOneBy([
            'user' => $user,
            'tontine' => $tontine
        ], ['id' => 'DESC']);

        //if tontine compteur is sup or equal to last cotisation tour, user has paid
        if ($lastCotisation && ($lastCotisation->getTour() >= $tontine->getCompteur())) {
            return true;
        }

        return false;
    }
}