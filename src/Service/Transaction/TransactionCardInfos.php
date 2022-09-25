<?php

namespace App\Service\Transaction;

use App\Entity\Tontine;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TransactionCardInfos
{
    public function __construct(
        private EntityManagerInterface $manager
    )
    {
    }

    public function __invoke(
        Transaction $transaction
    ) : array
    {
        $cardInfos = [];

        $signe = '-';
        if(in_array($transaction->getType(), Transaction::ENTREE)){
            $signe = '+';
        }

        $cardInfos['id'] = $transaction->getId();
        $cardInfos['montant'] = $transaction->getMontant();
        $cardInfos['signe'] = $signe;
        $cardInfos['type'] = $this->getTransactionTypeLibelle($transaction->getType());
        $cardInfos['date'] = $transaction->getCreatedAt()->format('d/m/Y H:i');
        $cardInfos['state'] = $this->getTransactionStatusLibelle($transaction->getState());
        $cardInfos['operateur'] = $this->getOperateurLibelle($transaction->getType());
        $cardInfos['sdr'] = $this->getObjTransactionRcvSdr($transaction->getIdSdr(), $transaction->getTypeSdr());
        $cardInfos['rcv'] = $this->getObjTransactionRcvSdr($transaction->getIdRcv(), $transaction->getTypeRcv());

        return $cardInfos;
    }

    public function getTransactionTypeLibelle(string $type): string
    {
        $libelle = '';
        switch ($type){
            case Transaction::TYPE_DEPOT:
                $libelle = 'Dépôt';
                break;
            case Transaction::TYPE_COTISATION:
                $libelle = 'Cotisation';
                break;
            case Transaction::TYPE_RETRAIT:
                $libelle = 'Retrait';
                break;
            case Transaction::TYPE_TRANSFERT:
                $libelle = 'Transfert';
                break;
            case Transaction::TYPE_DEPOT_COTISATION:
                $libelle = 'Dépôt cotisation';
                break;
        }
        return $libelle;
    }

    public function getTransactionStatusLibelle(string $status): string
    {
        $libelle = '';
        switch ($status){
            case Transaction::STATUS_EN_COURS:
            case Transaction::STATUS_EN_COURS_MTN:
                $libelle = 'En cours';
                break;
            case Transaction::STATUS_TERMINE:
                $libelle = 'Terminé';
                break;
            case Transaction::STATUS_ECHEC:
                $libelle = 'Echec';
            case Transaction::STATUS_ANNULE:
                $libelle = 'Annulé';
                break;
        }
        return $libelle;
    }

    public function getOperateurLibelle(string $type): string
    {
        $libelle = '';
        switch ($type){
            case Transaction::TYPE_DEPOT:
            case Transaction::TYPE_RETRAIT:
                $libelle = 'MTN MoMo';
                break;
            default:
                $libelle = 'App Tontine';
                break;
        }
        return $libelle;
    }

    public function getObjTransactionRcvSdr(int $id, string $type){
        if ($type === 'user'){
            $obj = $this->manager->getRepository(User::class)->find($id);
            return "{$obj->getNom()} {$obj->getPrenom()} | {$obj->getUsername()}";
        }elseif ($type === 'tontine'){
            $obj = $this->manager->getRepository(Tontine::class)->find($id);
            return "Tontine {$obj->getNom()}";
        }

        return 'Système';
    }
}