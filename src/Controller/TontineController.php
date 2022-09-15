<?php

namespace App\Controller;

use App\Entity\MontantTontine;
use App\Entity\PeriodiciteTontine;
use App\Entity\Tontine;
use App\Entity\Transaction;
use App\Entity\TypeTontine;
use App\Entity\User;
use App\Entity\UserTontine;
use App\Repository\MontantTontineRepository;
use App\Repository\PeriodiciteTontineRepository;
use App\Repository\TransactionRepository;
use App\Repository\TypeTontineRepository;
use App\Service\Tontine\AddMember;
use App\Service\Tontine\CreateTontine;
use App\Service\Tontine\GetUserTontintes;
use App\Service\Tontine\RemoveMember;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tontine')]
class TontineController extends AbstractController
{
    #[Route('/montant', name: 'tontine_montant_all', methods: ['GET'])]
    public function findAllMontantTontine(EntityManagerInterface $manager): JsonResponse
    {
        $montantTontine = $manager->getRepository(MontantTontine::class)->findAll();
        return $this->json($montantTontine, 200);
    }

    #[Route('/periodicite', name: 'tontine_periodicite_all', methods: ['GET'])]
    public function findAllPeriodiciteTontine(EntityManagerInterface $manager): JsonResponse
    {
        $periodiciteTontine = $manager->getRepository(PeriodiciteTontine::class)->findAll();
        return $this->json($periodiciteTontine, 200);
    }

    #[Route('/create', name: 'app_tontine')]
    public function index(Request $request, CreateTontine $createTontine, EntityManagerInterface $manager): JsonResponse
    {
        //Retrieve Post data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();
        try{
            $data['createdBy'] = $user;
            $data['montant'] = $manager->getRepository(MontantTontine::class)->find($data['montant']);
            $data['periodicite'] = $manager->getRepository(PeriodiciteTontine::class)->find($data['periodicite']);
            $tontine = $createTontine(...$data);
            $manager->persist($tontine);
            $manager->flush();
        }catch (\Exception $e){
            return $this->json([
                'message' => 'Internal server error _ ' . $e->getMessage(),
            ], 500);
        }

        return $this->json([
            'message' => 'Tontine created',
        ], 201);
    }

    #[Route('/add-member', name: 'app_tontine_add_member', methods: ['POST'])]
    public function addMember(Request $request, AddMember $addMember, EntityManagerInterface $manager): JsonResponse
    {
        //Retrieve Post data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();
        try{
            //Find Tontine and User to add
            $tontine = $manager->getRepository(Tontine::class)->find($data['tontine']);
            $userToAdd = $manager->getRepository(User::class)->find($data['user']);
            //If user doesnt exist
            if(!$userToAdd){
                return $this->json([
                    'message' => 'User doesnt exist',
                ], 404);
            }
            //If tontine doesnt exist
            if(!$tontine){
                return $this->json([
                    'message' => 'Tontine doesnt exist',
                ], 404);
            }
            //If user is not creator of tontine
            if($tontine->getCreatedBy() !== $user){
                return $this->json([
                    'message' => 'You are not creator of this tontine',
                ], 403);
            }
            $addMember($userToAdd, $tontine);
        }catch (\Exception $e){
            return $this->json([
                'message' =>  $e->getMessage(),
            ], 500);
        }
        return $this->json([
            'message' => 'Member added',
        ], 201);
    }

    #[Route('/remove-member', name: 'app_tontine_remove_member', methods: ['POST'])]
    public function removeMember(Request $request, RemoveMember $removeMember, EntityManagerInterface $manager): JsonResponse
    {
        //Retrieve Post data
        $data = json_decode($request->getContent(), true);
        //Get actual User
        /** @var User $user */
        $user = $this->getUser();
        try{
            //Find Tontine and User to add
            $tontine = $manager->getRepository(Tontine::class)->find($data['tontine']);
            $userToRemove = $manager->getRepository(User::class)->find($data['user']);
            //If user doesnt exist
            if(!$userToRemove){
                return $this->json([
                    'message' => 'User doesnt exist',
                ], 404);
            }
            //If tontine doesnt exist
            if(!$tontine){
                return $this->json([
                    'message' => 'Tontine doesnt exist',
                ], 404);
            }
            //If user is not creator of tontine
            if($tontine->getCreatedBy() !== $user){
                return $this->json([
                    'message' => 'You are not creator of this tontine',
                ], 403);
            }
            $removeMember($userToRemove, $tontine);
        }catch (\Exception $e){
            return $this->json([
                'message' =>  $e->getMessage(),
            ], 500);
        }
        return $this->json([
            'message' => 'Member removed',
        ], 201);
    }

    #[Route('/my-tontines', name: 'app_tontine_all', methods: ['GET'])]
    public function findAllTontine(GetUserTontintes $getUserTontintes, TransactionRepository $repository): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $tontines = $getUserTontintes($user);
        return $this->json(array_map(function(Tontine $tontine) use ($repository) {
            return [
                'id' => $tontine->getId(),
                'nom' => $tontine->getNom(),
                'montant' => $tontine->getMontant()->getValeur(),
                'periodicite' => $tontine->getPeriodicite()->getValue(),
                'createdBy' => $tontine->getCreatedBy()->getUsername(),
                'members' => array_map(function(UserTontine $user){
                    return [
                        'id' => $user->getId(),
                        'username' => $user->getUser()->getUsername(),
                        'nom' => $user->getUser()->getNom(),
                        'prenom' => $user->getUser()->getPrenom(),
                    ];
                }, $tontine->getMembres()->toArray()),
                'history' => array_map(function(Transaction $historique){
                    return [
                        'id' => $historique->getId(),
                        'montant' => $historique->getMontant(),
                        'state' => $historique->getState(),
                        'createdAt' => $historique->getCreatedAt()->format('Y-m-d H:i:s'),
                    ];
                }, $repository->getTontinesTransactions($tontine)),
            ];
        }, $tontines), 200);
    }

    #[Route('/montants', name: 'app_tontine_montants', methods: ['GET'])]
    public function findAllMontant(MontantTontineRepository $repository): JsonResponse
    {
        $montants = $repository->findAll();
        return $this->json(array_map(function(MontantTontine $montant){
            return [
                'id' => $montant->getId(),
                'value' => $montant->getValeur(),
            ];
        }, $montants), 200);
    }

    #[Route('/periodicites', name: 'app_tontine_periodicites', methods: ['GET'])]
    public function findAllPeriodicite(PeriodiciteTontineRepository $repository): JsonResponse
    {
        $periodicites = $repository->findAll();
        return $this->json(array_map(function(PeriodiciteTontine $periodicite){
            return [
                'id' => $periodicite->getId(),
                'value' => $periodicite->getValue(),
            ];
        }, $periodicites), 200);
    }

    #[Route('/types', name: 'app_tontine_types', methods: ['GET'])]
    public function findAllType(TypeTontineRepository $repository): JsonResponse
    {
        $types = $repository->findAll();
        return $this->json(array_map(function(TypeTontine $type){
            return [
                'id' => $type->getId(),
                'value' => $type->getValue(),
            ];
        }, $types), 200);
    }
}
