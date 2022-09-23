<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZReactController extends AbstractController
{
    #[Route('/{anystring}', name: 'app_react')]
    public function index(): Response
    {
        return $this->render('react/index.html.twig', []);
    }
}
