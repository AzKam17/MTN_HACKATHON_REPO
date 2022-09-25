<?php

namespace App\Service\Tontine;

use App\Entity\Tontine;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TontineRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Transaction\Transfert;

class DepotGainTontine
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TontineRepository $tontineRepository,
        private TransactionRepository $transactionRepository
    )
    {
    }

    public function __invoke()
    {
        //Get all tontines where compteur > 0
        $tontines = $this->tontineRepository->findStartedTontines();

        //For each tontine compute avancement and select tontine with pourcentage equal to 100
        $tontinesToClose = [];
        foreach ($tontines as $tontine) {
            $avancement = $tontine->getAvancement();
            if ($avancement['pourcentage'] === 100) {
                $tontinesToClose[] = $tontine;
            }
        }

        //For each tontine to close
        foreach ($tontinesToClose as $tontineToClose) {
            $liste = $tontineToClose->getListeRetrait();
            $membres = $liste->getMembres();
            //$membres is an array of associative array
            //Get the index of first array of $membres where isActive is false
            $index = array_search(false, array_column($membres, 'isActive'));
            $membre = $membres[$index];
            $membre['isActive'] = true;
            $membres[$index] = $membre;

            $userId = $membre['id'];
            //Get the user that will receive the gain
            $user = $this
                ->entityManager
                ->getRepository(User::class)
                ->find($userId);

            //Get the gain
            $gain = $tontineToClose->getMontant()->getValeur();

            $user->setSolde($user->getSolde() + ($gain * count($membres)));
            $tontineToClose->setSolde($tontineToClose->getSolde() - ($gain * count($membres)));
            $tontineToClose->setCompteur( $tontineToClose->getCompteur() + 1 );

            $transaction = (new Transaction())
                ->setType(Transaction::TYPE_DEPOT_COTISATION)
                ->setState(Transaction::STATUS_TERMINE)
                ->setIdSdr($tontineToClose->getId())
                ->setIdRcv($user->getId())
                ->setTypeRcv('user')
                ->setTypeSdr('tontine')
                ->setMontant($gain);

            $allActive = true;
            foreach ($membres as $membre){
                $allActive &= $membre['isActive'];
            }
            if($allActive){
                $newMembres = [];
                foreach ($membres as $membre){
                    $membre['isActive'] = false;
                    $newMembres[] = $membre;
                }
                $membres = $newMembres;
            }

            $liste->setMembres($membres);
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        }
    }
}