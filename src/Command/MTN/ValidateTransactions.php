<?php

namespace App\Command\MTN;
use App\Entity\Params;
use App\Entity\Transaction;
use App\Message\ValidateTransactionMessage;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'mtn:validate',
    description: 'Valider les transactions',
)]
class ValidateTransactions extends Command
{
    public function __construct(
        private TransactionRepository $repo,
        private MessageBusInterface $bus,
        $name = null
    )
    {
        parent::__construct($name);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $transactions = $this->repo->getAllPendingTransactions();
        foreach ($transactions as $transaction){
            $this->bus->dispatch(
                (new ValidateTransactionMessage(
                    $transaction['type'],
                    $transaction['uuid']
                ))
            );
        }
        return Command::SUCCESS;
    }
}