<?php

namespace App\MessageHandler;

use App\Message\DepotGain;
use App\Service\Tontine\DepotGainTontine;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DepotGainHandler
{
    public function __construct(
        private DepotGainTontine $depotGainTontine,)
    {
    }

    public function __invoke(
        DepotGain $depotGain
    )
    {
        ($this->depotGainTontine)();
    }
}