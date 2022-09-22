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
            case 'tontine_cotisation':
                //Retrive tontine from transaction idRcv
                $tontine = $this->entityManager->getRepository(Tontine::class)->find($transactionArray['idRcv']);
                $transactionArray['lib'] = "Cotisation - Tontine {$tontine->getNom()} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case 'depot':
                $transactionArray['lib'] = "Dépôt sur compte - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case 'retrait':
                $transactionArray['lib'] = "Retrait sur compte - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case 'transfert':
                // Retrive receiver from transaction idRcv
                $receiver = $this->entityManager->getRepository(User::class)->find($transactionArray['idRcv']);
                $transactionArray['lib'] = "Transfert - {$receiver->getNom()} {$receiver->getPrenom()} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case 'depot_cotisation':
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