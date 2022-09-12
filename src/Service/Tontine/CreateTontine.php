<?php

namespace App\Service\Tontine;

use App\Entity\ListeRetrait;
use App\Entity\MontantTontine;
use App\Entity\PeriodiciteTontine;
use App\Entity\Tontine;
use App\Entity\User;
use App\Entity\UserTontine;
use Doctrine\ORM\EntityManagerInterface;

class CreateTontine
{
    public function __construct(
    ){}

    public function __invoke(
        string $nom,
        MontantTontine $montant,
        PeriodiciteTontine $periodicite,
        User $createdBy,
        int $compteur = 0,
        int $solde = 0,
    ) : Tontine
    {
        $tontine = new Tontine();
        $tontine->setNom($nom);
        $tontine->setMontant($montant);
        $tontine->setPeriodicite($periodicite);
        $tontine->setCompteur($compteur);
        $tontine->setSolde($solde);
        $tontine->setCreatedBy($createdBy);
        $tontine->setListeRetrait(
            (new ListeRetrait())
            ->setMembres(
                [
                    [
                        'position' => 1,
                        'id' => $createdBy->getId(),
                        'isActive' => false,
                    ]
                ]
            )
        )
        ->addMembre(
            (new UserTontine())
            ->setUser($createdBy)
            ->setIsRemoved(false)
        );
        return $tontine;
    }
}