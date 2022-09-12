<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Transaction\Transfert;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/transaction')]
class TransactionController extends AbstractController
{
    #[Route('/transfert', name: 'app_transaction')]
    public function transfert(Transfert $transfert, Request $request, EntityManagerInterface $manager): JsonResponse
    {
        //Get POST data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();

        //Retrieve receiver user
        $receiver = $manager->getRepository(User::class)->find($data['receiver']);

        //If receiver doesn't exist, return error
        if(!$receiver){
            return $this->json([
                'message' => 'Receiver not found',
            ], 404);
        }

        $montant = $data['montant'] ?? 0;

        try{
            $transfert(
                $user, $receiver, 'user', 'user', 'transfert', $montant
            );
        }catch (\Exception $e){
            return $this->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return $this->json([
            'message' => 'Transaction done',
        ], 201);
    }
}
