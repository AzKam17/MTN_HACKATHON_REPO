<?php

namespace App\Controller;

use App\Entity\Tontine;
use App\Entity\Transaction;
use App\Entity\User;
use App\Service\Transaction\AddLibToTransactionArray;
use App\Service\Transaction\CotisationTontine;
use App\Service\Transaction\RechargementTontine;
use App\Service\Transaction\Retrait;
use App\Service\Transaction\TransactionCardInfos;
use App\Service\Transaction\Transfert;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/transaction')]
class TransactionController extends AbstractController
{
    #[Route('/transfert', name: 'app_transaction', methods: ['POST'])]
    public function transfert(
        Transfert $transfert,
        Request $request,
        EntityManagerInterface $manager,
        AddLibToTransactionArray $addLibToTransactionArray
    ): JsonResponse
    {
        //Get POST data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();

        //Retrieve receiver user by tel
        $receiver = $manager->getRepository(User::class)->findOneBy(['tel' => $data['receiverTel']]);

        //If receiver doesn't exist, return error
        if(!$receiver){
            return $this->json([
                'message' => 'Receiver not found',
            ], 404);
        }

        $montant = $data['montant'] ?? 0;

        try{
            $result = $transfert(
                $user,
                $receiver,
                'user', 'user', 'transfert', $montant, Transaction::STATUS_TERMINE
            );
        }catch (\Exception $e){
            return $this->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return $this->json([
            'message' => 'Transaction done',
            'result' => $addLibToTransactionArray($result)
        ], 201);
    }

    #[Route('/tontine/cotisation', name: 'app_transaction_tontine_cotisation', methods: ['POST'])]
    public function tontine(
        CotisationTontine $cotisationTontine,
        Request $request,
        EntityManagerInterface $manager,
        AddLibToTransactionArray $addLibToTransactionArray
    ): JsonResponse
    {
        //Get POST data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();

        //Retrieve receiver user
        $receiver = $manager->getRepository(Tontine::class)->find($data['idTontine']);

        //If receiver doesn't exist, return error
        if(!$receiver){
            return $this->json([
                'message' => 'Tontine not found',
            ], 404);
        }

        $montant = $data['montant'] ?? 0;

        try{
            $result = $cotisationTontine(
                $user, $receiver, 'user', 'tontine', Transaction::TYPE_COTISATION, $montant
            );
        }catch (\Exception $e){
            return $this->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return $this->json([
            'message' => 'Transaction done',
            'result' => $addLibToTransactionArray($result)
        ], 201);
    }

    #[Route('/retrait', name: 'app_transaction_retrait', methods: ['POST'])]
    public function retrait(
        Retrait $retrait,
        Request $request,
        EntityManagerInterface $manager,
        AddLibToTransactionArray $addLibToTransactionArray
    ): JsonResponse
    {
        //Get POST data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();

        $montant = $data['montant'] ?? 0;
        $admin = $manager
            ->getRepository(User::class)
            ->findOneBy(
                ['slug' => 'admin-admin-0779136356']
            );

        try{
            $result = $retrait(
                $admin, $user, $montant
            );
        }catch (\Exception $e){
            return $this->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return $this->json([
            'message' => 'Transaction done',
            'object' => $addLibToTransactionArray($result),
        ], 201);
    }

    #[Route('/rechargement', name: 'app_transaction_rechargement', methods: ['POST'])]
    public function rechargement(
        RechargementTontine $rechargementTontine,
        Request $request,
        EntityManagerInterface $manager,
        AddLibToTransactionArray $addLibToTransactionArray
    ): JsonResponse
    {
        //Get POST data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();

        $montant = $data['montant'] ?? 0;
        try{
            $result = $rechargementTontine(
                $user, $montant
            );
        }catch (\Exception $e){
            return $this->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return $this->json([
            'message' => 'Transaction done',
            'object' => $addLibToTransactionArray($result),
        ], 201);
    }

    #[Route('/card/{id}', name: 'app_transaction_card', methods: ['GET'])]
    public function card(
        Transaction $transaction,
        TransactionCardInfos $cardInfos
    ): JsonResponse
    {
        //If transaction is not found return error
        if(!$transaction){
            return $this->json([
                'message' => 'Transaction not found',
            ], 404);
        }

        return $this->json([
            'message' => 'Transaction Information',
            'object' => $cardInfos($transaction),
        ], 200);
    }
}
