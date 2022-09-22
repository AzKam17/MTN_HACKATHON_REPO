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
        private TontineRepository $tontineRepository,
        private AddLibToTransactionArray $addLibToTransactionArray,
    )
    {
    }

    public function __invoke(
        User $user
    ): array
    {
        $tontineRepository = $this->tontineRepository;
        $transactions = $this->repository->getUsersTransactions($user);
        $addLibToTransactionArray = $this->addLibToTransactionArray;
        $final_transac = array_map(function (Transaction $transaction) use ($addLibToTransactionArray) {
            return $addLibToTransactionArray($transaction);
        }, $transactions);
        usort(
            $final_transac,
            //Sort by id desc
            function ($a, $b) {
                return $b['id'] <=> $a['id'];
            }
        );
        return $final_transac;
    }
}