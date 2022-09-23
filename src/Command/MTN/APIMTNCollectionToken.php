<?php

namespace App\Command\MTN;

use App\Entity\Params;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'mtn:collection:token',
    description: 'Generer un token pour l\'API de collection',
)]
class APIMTNCollectionToken extends Command
{
    public function __construct(
        private ParameterBagInterface $params,
        private EntityManagerInterface $em,
        $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $apiCollectionUser = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'USER_COLLECTION']);
        $apiCollectionPassword = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'API_KEY_COLLECTION']);
        $basicAuth = base64_encode($apiCollectionUser->getValue() . ':' . $apiCollectionPassword->getValue());

        $client = new Client(HttpClient::create(['headers' =>
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Basic {$basicAuth}",
                'Ocp-Apim-Subscription-Key' => $this->params->get('COLLECTION_PRIMARY_KEY'),
            ]
        ]));

        //Send json data to the API to create api user
        $crawler = $client->request(
            'POST',
            'https://sandbox.momodeveloper.mtn.com/collection/token/',
            [], [], [], "");

        //If the API returns a 201 status code, then the account was created successfully
        if ($client->getResponse()->getStatusCode() == 200) {
            $output->writeln(
                [
                    'Token generated successfully',
                    '============',
                    '',
                ]
            );
            $token = json_decode($client->getResponse()->getContent(), true);
            $apiCollectionToken = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'TOKEN_COLLECTION']);
            //If not exist, create it
            if (!$apiCollectionToken) {
                $apiCollectionToken = new Params();
                $apiCollectionToken->setLib('TOKEN_COLLECTION');
            }
            $apiCollectionToken->setValue(
                $token['access_token']
            )
            ->setExpiresAt(
                   //Convert the expires_in value to a DateTime object using Carbon
                    Carbon::now()->addSeconds($token['expires_in'])
            );
            $this->em->persist($apiCollectionToken);
            $this->em->flush();
        } else {
            $io = new SymfonyStyle($input, $output);
            $io->error('Account creation failed');
            $output->writeln($client->getResponse()->getContent());
        }
        return Command::SUCCESS;
    }
}
