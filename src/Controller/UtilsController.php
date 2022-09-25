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

#[Route('/api/utils')]
class UtilsController extends AbstractController
{
    //Validate Transactions
    #[Route('/validate-transactions', name: 'validate_transactions', methods: ['GET'])]
    public function validateTransactions(
    ): JsonResponse {
        shell_exec('php bin/console mtn:validate');;
        return $this->json(
            [
                'message' => 'Transactions validated',
            ]
        );
    }
}
