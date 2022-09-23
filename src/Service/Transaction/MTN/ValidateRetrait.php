<?php

namespace App\Service\Transaction\MTN;

use App\Entity\Params;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Uid\Uuid;

class ValidateRetrait
{
    public function __construct(
        private ParameterBagInterface $params,
        private EntityManagerInterface $em,
    )
    {
    }

    public function __invoke(
        string $uuid
    )
    {
        do {
            $token = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'TOKEN_DISBURSEMENTS']);
            //if token is not set, generate a new one
            if (!$token) {
                shell_exec('php bin/console mtn:disbursements:token');
                sleep(10);
                //Get the new token
                $token = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'TOKEN_DISBURSEMENTS']);
            }else{
                //If token expired, execute cli command to get new token
                if ($token->getExpiresAt() < new \DateTimeImmutable('-1 hour')) {
                    shell_exec('php bin/console mtn:disbursements:token');
                    //sleep for 10 seconds to wait for the token to be updated
                    sleep(10);
                    //Get the new token
                    $token = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'TOKEN_DISBURSEMENTS']);
                }
            }
        } while ($token->getExpiresAt() < new \DateTimeImmutable('-1 hour'));

        $curl = new \anlutro\cURL\cURL;
        $request = $curl->newRequest(
            'get',
            "https://sandbox.momodeveloper.mtn.com/disbursement/v1_0/transfer/{$uuid}")
            ->setHeader('Authorization', 'Bearer '.$token->getValue())
            ->setHeader('X-Target-Environment', 'sandbox')
            ->setHeader('Ocp-Apim-Subscription-Key', $this->params->get('DISBURSEMENTS_PRIMARY_KEY'));
        $request = $request->send();

        //If the response is 202, the transaction is successful
        $transaction = $this->em->getRepository(Transaction::class)->findOneBy(['additionalData' => $uuid]);
        if ($request->statusCode === 200) {
            $data = json_decode($request->body, true);
            dump($data);
            switch ($data['status']){
                case 'PENDING':
                    $transaction->setState('pending_mtn');
                    break;
                case 'SUCCESSFUL':
                    $transaction->setState('success');
                    break;
                default:
                    $transaction->setState('failed');
                    break;
            }
        }else{
            $transaction->setState('failed');
        }
        $this->em->flush();
        return true;
    }
}