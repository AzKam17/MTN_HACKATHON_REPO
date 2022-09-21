<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TontineRepository;
use App\Repository\TransactionRepository;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserTransactions
{
    public function __construct(
        private TransactionRepository $repository,
        private TontineRepository $tontineRepository
    )
    {
    }

    public function __invoke(
        User $user
    ): array
    {
        $tontineRepository = $this->tontineRepository;
        $transactions = $this->repository->getUsersTransactions($user);
        $final_transac = array_map(function (Transaction $transaction) use ($tontineRepository) {
            return array_merge([
                'id' => $transaction->getId(),
                'idSdr' => $transaction->getIdSdr(),
                'idRcv' => $transaction->getIdRcv(),
                'createdAt' => $transaction->getCreatedAt(),
                'typeRcv' => $transaction->getTypeRcv(),
                'typeSdr' => $transaction->getTypeSdr(),
                'state' => $transaction->getState(),
                'montant' => $transaction->getMontant(),
                'type' => $transaction->getType(),
            ],
                // If typeRcv or typeSdr is tontine then add tontine
                (
                $transaction->ifTontine()
                    ?
                    [
                        'tontine' => [
                            'id' => $transaction->getTontineId(),
                            'name' => $tontineRepository->find($transaction->getTontineId())->getNom(),
                            'solde' => $tontineRepository->find($transaction->getTontineId())->getSolde()
                        ]
                    ] : []
                )
            );
        }, $transactions);
        usort(
            $final_transac,
            //Sort by id desc
            function ($a, $b) {
                return $b['id'] - $a['id'];
            }
        );
        return $final_transac;
    }
}