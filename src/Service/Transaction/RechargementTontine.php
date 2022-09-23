<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class RechargementTontine
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Transfert $transfert
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        User $client,
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
            $client,
            'provider',
            'user',
            Transaction::TYPE_DEPOT,
            $montant
        );
    }
}