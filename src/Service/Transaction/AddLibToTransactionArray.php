<?php

namespace App\Service\Transaction;

use App\Entity\Tontine;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

//To use when asked by User
class AddLibToTransactionArray
{
    //Add EntityManager to constructor

    public function __construct(
        private TransactionCardInfos $transactionCardInfos,
        private EntityManagerInterface $entityManager,
        private  Security $security
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
                $transactionArray['lib'] = "Tontine {$tontine->getNom()} - {$this->transactionCardInfos->getTransactionStatusLibelle($transactionArray['state'])} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case Transaction::TYPE_RETRAIT:
            case Transaction::TYPE_DEPOT:
                $transactionArray['lib'] = "{$this->transactionCardInfos->getTransactionStatusLibelle($transactionArray['state'])} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            case Transaction::TYPE_TRANSFERT:
                // Retrive receiver from transaction idRcv
                // if receiver is the user
                /** @var User $user */
                $user = $this->security->getUser();
                $message = "{$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                if ($transactionArray['idRcv'] === $user->getId()) {
                    $sender = $this->entityManager->getRepository(User::class)->find($transactionArray['idSdr']);
                    $transactionArray['lib'] = "De {$sender->getNom()} {$sender->getPrenom()} - {$this->transactionCardInfos->getTransactionStatusLibelle($transactionArray['state'])} - {$message}";
                } else {
                    $receiver = $this->entityManager->getRepository(User::class)->find($transactionArray['idRcv']);
                    $transactionArray['lib'] = "À {$receiver->getNom()} {$receiver->getPrenom()} - {$this->transactionCardInfos->getTransactionStatusLibelle($transactionArray['state'])} - {$message}";
                }
                break;
            case Transaction::TYPE_DEPOT_COTISATION:
                //Retrive tontine from transaction idRcv
                $tontine = $this->entityManager->getRepository(Tontine::class)->find($transactionArray['idSdr']);
                $transactionArray['lib'] = "Versement - {$tontine->getNom()} - {$this->transactionCardInfos->getTransactionStatusLibelle($transactionArray['state'])} - {$transaction->getCreatedAt()->format('d/m/Y - H:i')}";
                break;
            default:
                $transactionArray['lib'] = 'Inconnu';
                break;
        }

        return $transactionArray;
    }
}