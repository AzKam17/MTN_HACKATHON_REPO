<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DepotCompte
{
    public function __construct(
        private EntityManagerInterface $entityManager,
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
        //Start transaction
        $this->entityManager->getConnection()->beginTransaction();
        try{

            //Retrieve Admin User
            $admin = $this
                ->entityManager
                ->getRepository(User::class)
                ->findOneBy(
                    ['slug' => 'admin-admin-0779136356']
                );

            $admin->setSolde($admin->getSolde() - $montant);
            $user->setSolde($user->getSolde() + $montant);
            //Create Transaction
            $transaction = new Transaction();
            $transaction->setIdSdr($admin->getId());
            $transaction->setIdRcv($user->getId());
            $transaction->setTypeSdr('admin');
            $transaction->setTypeRcv('user');
            $transaction->setState('done');
            $transaction->setMontant($montant);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return $transaction;
        }catch (\Exception $e) {
            //Rollback transaction
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }
}