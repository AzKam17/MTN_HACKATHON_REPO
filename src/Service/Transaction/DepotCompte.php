<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DepotCompte
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Transfert $transfert,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        User $user,
        float $montant
    ) : Transaction
    {
        $admin = $this
            ->entityManager
            ->getRepository(User::class)
            ->findOneBy(
                ['slug' => 'admin-admin-0779136356']
            );
        return $this->transfert->__invoke(
            $admin,
            $user,
            'admin',
            'user',
            'depot-admin',
            $montant
        );
    }
}