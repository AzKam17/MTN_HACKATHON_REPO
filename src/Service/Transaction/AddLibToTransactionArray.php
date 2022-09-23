<?php

namespace App\Service\Transaction;

use App\Entity\Tontine;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

//To use when asked by User
class AddLibToTransactionArray
{
    //Add EntityManager to constructor
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        Transaction $transaction
    ) : array
    {
        $transactionArray = $transaction->toArray();

        switch ($transactionArray['type']) {
            case Transaction::TYPE_COTISATION:
                //Retrive tontine from transaction idRcv
                $tontine = $this->entityManager->getRepository(Tontine::class)->find($transactionArray['idRcv']);
                $transactionArray['lib'] = "Cotisation - Tontine {$tontine->getNom()} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case Transaction::TYPE_DEPOT:
                $transactionArray['lib'] = "Dépôt sur compte - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case Transaction::TYPE_RETRAIT:
                $transactionArray['lib'] = "Retrait sur compte - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case Transaction::TYPE_TRANSFERT:
                // Retrive receiver from transaction idRcv
                $receiver = $this->entityManager->getRepository(User::class)->find($transactionArray['idRcv']);
                $transactionArray['lib'] = "Transfert - {$receiver->getNom()} {$receiver->getPrenom()} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case Transaction::TYPE_DEPOT_COTISATION:
                //Retrive tontine from transaction idRcv
                $tontine = $this->entityManager->getRepository(Tontine::class)->find($transactionArray['idSdr']);
                $transactionArray['lib'] = "Versement Tontine - Tontine {$tontine->getNom()} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            default:
                $transactionArray['lib'] = 'Inconnu';
                break;
        }

        return $transactionArray;
    }
}