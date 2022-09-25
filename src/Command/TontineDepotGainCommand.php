<?php

namespace App\Command;

use App\Message\DepotGain;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'tontine:depot-gain',
    description: 'Commande assurant le depot des gains de tontines sur les comptes des beneficiaires',
)]
class TontineDepotGainCommand extends Command
{
    public function __construct(
        private MessageBusInterface $bus,
        $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->bus->dispatch(
            (new DepotGain())
        );
        $io->success('Les gains ont été deposés avec succès.');

        return Command::SUCCESS;
    }
}
