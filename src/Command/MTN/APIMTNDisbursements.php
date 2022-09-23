<?php

namespace App\Command\MTN;

use App\Entity\Params;
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
    name: 'mtn:disbursements',
    description: 'Creer un compte pour l\'API de disbursement',
)]
class APIMTNDisbursements extends Command
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

        //Generate uuid v4
        $uuid = Uuid::v4();
        $client = new Client(HttpClient::create(['headers' =>
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Reference-Id' => $uuid,
                'Ocp-Apim-Subscription-Key' => $this->params->get('DISBURSEMENTS_PRIMARY_KEY'),
                'X-Target-Environment' => 'sandbox',
            ]
        ]));

        //Send json data to the API to create api user
        $crawler = $client->request('POST', 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser', [], [], [], '{
            "providerCallbackHost": "https://mtn-hackathon.herokuapp.com"
        }');

        //If the API returns a 201 status code, then the account was created successfully
        if ($client->getResponse()->getStatusCode() == 201) {
            $crawler = $client->request(
                'POST',
                'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/'. $uuid .'/apikey',
                [], [], []);

            //If the API returns a 201 status code, then the api key was created successfully
            //We save the api key and uuid in the database
            $data = json_decode($client->getResponse()->getContent(), true);

            $apiKeyDisbursements = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'API_KEY_DISBURSEMENTS']);
            $userDisbursements = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'USER_DISBURSEMENTS']);

            //If the api key and uuid already exist in the database, we update them
            if ($apiKeyDisbursements) {
                $apiKeyDisbursements->setValue($data['apiKey']);
                $userDisbursements->setValue($uuid);
            } else {
                //If the api key and uuid do not exist in the database, we create them
                $apiKeyDisbursements = new Params();
                $apiKeyDisbursements->setLib('API_KEY_DISBURSEMENTS');
                $apiKeyDisbursements->setValue($data['apiKey']);

                $userDisbursements = new Params();
                $userDisbursements->setLib('USER_DISBURSEMENTS');
                $userDisbursements->setValue($uuid);
            }

            $this->em->persist($userDisbursements);
            $this->em->persist($apiKeyDisbursements);
            $this->em->flush();

            $output->writeln(
                "Successfully created api user with uuid: " . $uuid
            );
        } else {
            $io = new SymfonyStyle($input, $output);
            $io->error('Account creation failed');
            $output->writeln($client->getResponse()->getContent());
        }
        return Command::SUCCESS;
    }
}
