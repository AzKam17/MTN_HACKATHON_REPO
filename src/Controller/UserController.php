<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    //Get actuel user id
    #[Route('/id', name: 'user_id', methods: ['GET'])]
    public function findUserId(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->json([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'tel' => $user->getTel(),
            'solde' => $user->getSolde(),
        ], 200);
    }

    //Get user id from tel
    #[Route('/id/{tel}', name: 'user_id_from_tel', methods: ['GET'])]
    public function findUserIdFromTel(string $tel, EntityManagerInterface $manager): JsonResponse
    {
        $user = $manager->getRepository(User::class)->findOneBy(['tel' => $tel]);
        if ($user) {
            return $this->json([
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'tel' => $user->getTel(),
                'solde' => $user->getSolde(),
            ], 200);
        }
        return $this->json(['message' => 'User not found'], 404);
    }

    //Get User infos
    #[Route('/infos', name: 'user_infos', methods: ['GET'])]
    public function findUserInfos(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->json([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'tel' => $user->getTel(),
            'solde' => $user->getSolde(),
        ], 200);
    }

    //Get User infos from tel
    #[Route('/infos/{tel}', name: 'user_infos_from_tel', methods: ['GET'])]
    public function findUserInfosFromTel(string $tel, EntityManagerInterface $manager): JsonResponse
    {
        $user = $manager->getRepository(User::class)->findOneBy(['tel' => $tel]);
        if ($user) {
            return $this->json([
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'tel' => $user->getTel(),
                'solde' => $user->getSolde(),
            ], 200);
        }
        return $this->json(['message' => 'User not found'], 404);
    }
}
