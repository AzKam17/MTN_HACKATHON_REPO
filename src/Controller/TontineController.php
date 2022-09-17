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
            $tontine = $createTontine(
                $data['nom'],
                $manager->getRepository(MontantTontine::class)->find($data['montant']),
                $manager->getRepository(PeriodiciteTontine::class)->find($data['periodicite']),
                $manager->getRepository(TypeTontine::class)->find($data['type']),
                $user,
            );
            $manager->persist($tontine);
            $manager->flush();
        }catch (\Exception $e){
            return $this->json([
                'message' => 'Internal server error _ ' . $e->getMessage(),
                'result' => $tontine->toArray()
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
                'solde' => $tontine->getSolde(),
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
