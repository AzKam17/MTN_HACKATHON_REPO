<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TontineRepository;
use App\Repository\TransactionRepository;
use App\Service\Transaction\GetUserTransactions;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api/user')]
class UserController extends AbstractController
{
    //Find User Transactions
    #[Route('/transactions', name: 'user_transactions', methods: ['GET'])]
    public function findUserTransactions(GetUserTransactions $getUserTransactions): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $transactions = $getUserTransactions($user);
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
    public function findUserIdFromTel(
        string $tel,
        EntityManagerInterface $manager
    ): JsonResponse
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
    public function findUserInfos(
        TransactionRepository $transactionRepository,
        GetUserTransactions $getUserTransactions
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->json(array_merge(
            $user->toArray(),
            [
                'transactions' => $getUserTransactions($user),
            ]
        ), 200);
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

    //Change password

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/change-password', name: 'user_change_password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $manager,
        ContainerBagInterface $params
    ){
        //Get POST data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();

        if(!$user){
            return $this->json([
                'message' => 'User not found',
            ], 500);
        }

        $user->setPassword(
            $hasher->hashPassword(
                $user,
                $data['password']
            )
        );

        $manager->persist($user);
        $manager->flush();

        return $this->json([
            'message' => 'Password changed',
        ], 201);
    }
}
