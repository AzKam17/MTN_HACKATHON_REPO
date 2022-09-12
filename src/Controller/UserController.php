<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/user')]
class UserController extends AbstractController
{
    //Find User Transactions
    #[Route('/transactions', name: 'user_transactions', methods: ['GET'])]
    public function findUserTransactions(TransactionRepository $repository): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $transactions = $repository->getUsersTransactions($user);
        return $this->json($transactions, 200);
    }
}
