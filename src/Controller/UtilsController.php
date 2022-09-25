<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/utils')]
class UtilsController extends AbstractController
{
    //Validate Transactions
    #[Route('/validate-transactions', name: 'validate_transactions', methods: ['GET'])]
    public function validateTransactions(
        KernelInterface $kernel
    ): JsonResponse {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'mtn:validate',
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new NullOutput();
        $application->run($input, $output);

        return $this->json(
            [
                'message' => 'Transactions validated',
            ]
        );
    }
}
