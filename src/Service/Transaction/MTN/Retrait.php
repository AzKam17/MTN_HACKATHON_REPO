<?php

namespace App\Service\Transaction\MTN;

use App\Entity\Params;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class Retrait
{

    public function __construct(
        private ParameterBagInterface $params,
        private EntityManagerInterface $em,
    )
    {
    }

    public function __invoke(
        Transaction $transaction
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
                    //sleep for 7 seconds to wait for the token to be updated
                    sleep(10);
                    //Get the new token
                    $token = $this->em->getRepository(Params::class)->findOneBy(['lib' => 'TOKEN_DISBURSEMENTS']);
                }
            }
        } while ($token->getExpiresAt() < new \DateTimeImmutable('-1 hour'));

        //Generate uuid v4
        $uuid = (Uuid::v4())->toRfc4122();
        $curl = new \anlutro\cURL\cURL;
        $request = $curl->newJsonRequest(
            'post',
            'https://sandbox.momodeveloper.mtn.com/disbursement/v1_0/transfer',
            [
                'amount' => $transaction->getMontant(),
                'currency' => 'EUR',
                'externalId' => 'jkvbkjbkjb',
                'payee' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => ($this->em->getRepository(User::class)->find($transaction->getIdRcv()))->getTel()
                ],
                'payerMessage' => 'Retrait vers MTN Momo - Montant: '.$transaction->getMontant(),
                'payeeNote' => 'Retrait vers MTN Momo - Montant: '.$transaction->getMontant(),
            ]
        )
            ->setHeader('Authorization', 'Bearer '.$token->getValue())
            ->setHeader('X-Reference-Id', $uuid)
            ->setHeader('X-Target-Environment', 'sandbox')
            ->setHeader('Ocp-Apim-Subscription-Key', $this->params->get('DISBURSEMENTS_PRIMARY_KEY'));
        $request = $request->send();

        //If the response is 202, the transaction is successful
        if ($request->statusCode !== 202) {
            $transaction->setState('failed');
            $this->em->flush();
            return true;
        }
        $transaction->setAdditionalData($uuid);
        $transaction->setState('pending_mtn');
        $this->em->flush();
        return true;
    }
}