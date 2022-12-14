<?php

namespace App\Service\Transaction;

use App\Entity\Tontine;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

//To use when asked by another person than User
class AddLibToTransactionArrayOther
{
    //Add EntityManager to constructor
    public function __construct(
        private TransactionCardInfos $transactionCardInfos,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        Transaction $transaction
    ) : array
    {
        $transactionArray = $transaction->toArray();

        switch ($transactionArray['type']) {
            case 'tontine_cotisation':
                //Retrive tontine from transaction idRcv
                $sender = $this->entityManager->getRepository(User::class)->find($transactionArray['idSdr']);
                $transactionArray['lib'] = "Cotisation - {$sender->getNom()} {$sender->getPrenom()} - {$this->transactionCardInfos->getTransactionStatusLibelle($transactionArray['state'])} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case 'depot_cotisation':
                //Retrive tontine from transaction idRcv
                $receiver = $this->entityManager->getRepository(User::class)->find($transactionArray['idRcv']);
                $transactionArray['lib'] = "Versement - {$receiver->getNom()} {$receiver->getPrenom()} - {$this->transactionCardInfos->getTransactionStatusLibelle($transactionArray['state'])}- {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            default:
                $transactionArray['lib'] = 'Inconnu';
                break;
        }

        return $transactionArray;
    }
}