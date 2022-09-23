<?php

namespace App\MessageHandler;

use App\Entity\Transaction;
use App\Message\ValidateTransactionMessage;
use App\Service\Transaction\MTN\ValidateDepot;
use App\Service\Transaction\MTN\ValidateRetrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ValidateTransactionHandler
{
    public function __construct(
        private ValidateDepot $validateDepot,
        private ValidateRetrait $validateRetrait
    )
    {
    }

    public function __invoke(
        ValidateTransactionMessage $message
    )
    {
        switch ($message->getType()) {
            case Transaction::TYPE_DEPOT:
                $this->validateDepot->__invoke($message->getUuid());
                break;
            case Transaction::TYPE_RETRAIT:
                $this->validateRetrait->__invoke($message->getUuid());
                break;
        }
    }
}