<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class Retrait
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
        User $provider,
        User $client,
        float $montant
    ) : Transaction
    {
        // Fees for retrait equals 1%
        $fees = $montant * 0.01;
        //Total amount to be withdrawn
        $total = $montant + $fees;

        $tranfertWithdraw = $this->transfert->__invoke(
            $client,
            $provider,
            'user',
            'provider',
            Transaction::TYPE_RETRAIT,
            $montant
        );

        if ($tranfertWithdraw){
            $admin = $this
                ->entityManager
                ->getRepository(User::class)
                ->findOneBy(
                    ['slug' => 'admin-admin-0779136356']
                );
            $tranfertFees = $this->transfert->__invoke(
                $provider,
                $admin,
                'provider',
                'admin',
                'retrait-fees',
                $total
            );
        }
        return $tranfertWithdraw;
    }
}