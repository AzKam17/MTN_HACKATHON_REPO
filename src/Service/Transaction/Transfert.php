<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use App\Message\TransactionMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Transfert
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private MessageBusInterface $bus
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        User $sender,
        User $receiver,
        string $typeSender,
        string $typeReceiver,
        string $typeTransaction,
        float $montant
    ) : Transaction
    {
        //Start transaction
        $this->entityManager->getConnection()->beginTransaction();
        try{
            //Check if sender has enough money
            if($sender->getSolde() < $montant){
                throw new \Exception('Sender has not enough money');
            }

            //Update sender and receiver balance
            $sender->setSolde($sender->getSolde() - $montant);
            $receiver->setSolde($receiver->getSolde() + $montant);
            //Create Transaction
            $transaction = new Transaction();
            $transaction->setIdSdr($sender->getId());
            $transaction->setIdRcv($receiver->getId());
            $transaction->setTypeSdr($typeSender);
            $transaction->setTypeRcv($typeReceiver);
            $transaction->setType($typeTransaction);
            $transaction->setState('pending');
            $transaction->setMontant($montant);

            $errors = $this->validator->validate($transaction);
            if (count($errors) > 0) {
                throw new \Exception($errors->get(0)->getMessage());
            }

            //Persist
            $this->entityManager->persist($transaction);
            $this->entityManager->persist($sender);
            $this->entityManager->persist($receiver);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            $this->bus->dispatch(new TransactionMessage($transaction->getId()));
            return $transaction;
        }catch (\Exception $e) {
            //Rollback transaction
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }
}