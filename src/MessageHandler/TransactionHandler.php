<?php

namespace App\MessageHandler;

use App\Entity\Transaction;
use App\Message\TransactionMessage;
use App\Service\Transaction\MTN\Depot;
use App\Service\Transaction\MTN\Retrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TransactionHandler
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Depot $depot,
        private Retrait $retrait,
    )
    {
    }

    public function __invoke(TransactionMessage $message)
    {
        //Retrive the transaction
        $transaction = $this
            ->entityManager
            ->getRepository(Transaction::class)
            ->find($message->getId());

        //Set the state to pending
        $transaction->setState('pending');

        switch ($transaction->getType()) {
            case Transaction::TYPE_DEPOT:
                $this->depot->__invoke($transaction);
                break;
            case Transaction::TYPE_RETRAIT:
                $this->retrait->__invoke($transaction);
                break;
        }
    }
}