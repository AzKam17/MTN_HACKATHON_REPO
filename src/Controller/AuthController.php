<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CreateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/public/register', name: 'app_register', methods: ['POST'])]
    public function index(EntityManagerInterface $manager, Request $request, CreateUser $user): JsonResponse
    {
        //Get POST data
        $data = json_decode($request->getContent(), true);

        //Create user
        try {
            $result = $user(...$data);
            $manager->persist($result);
            $manager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Internal server error _ ' . $e->getMessage(),
            ], 500);
        }
        return $this->json([
            'message' => 'User created',
        ], 201);
    }

    #[Route('/api/public/phone_check/{phone}', name: 'app_phone_check', methods: ['GET'])]
    public function phoneCheck(string $phone, Request $request, EntityManagerInterface $manager): JsonResponse
    {
        //$phone = $request->query->get('phone');
        $user = $manager->getRepository(User::class)->findOneBy(['tel' => $phone]);
        if ($user) {
            return $this->json([
                'message' => 'Phone already exists',
            ], 409);
        }
        return $this->json([
            'message' => 'Phone available',
        ], 200);
    }
}
