<?php

namespace App\Service\Tontine;

use App\Entity\User;
use App\Entity\UserTontine;
use Doctrine\ORM\EntityManagerInterface;

class GetUserTontintes
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    public function __invoke(
        User $user,
    )
    {
        $userTontines = $this->em->getRepository(UserTontine::class)->findBy(['user' => $user]);
        $tontines = [];
        foreach ($userTontines as $userTontine) {
            if (!$userTontine->isIsRemoved()) {
                $tontines[] = $userTontine->getTontine();
            }
        }
        return $tontines;
    }
}