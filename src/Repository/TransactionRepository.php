<?php

namespace App\Repository;

use App\Entity\Tontine;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function add(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    //Get Tontine related transactions
    public function getTontinesTransactions(Tontine $tontine)
    {
        //Find all transactions where typeRcv or typeSdr is tontine and sender or receiver is equal to tontine id
        return $this->createQueryBuilder('t')
            ->where('t.typeRcv = :tontine')
            ->orWhere('t.typeSdr = :tontine')

            ->andWhere('t.idRcv = :tontineId')
            ->orWhere('t.idSdr = :tontineId')
            ->setParameter('tontine', 'tontine')
            ->setParameter('tontineId', $tontine->getId())
            ->getQuery()
            ->getResult();
    }

    //Get User related transactions
    public function getUsersTransactions(User $user)
    {
        //Find all transactions where typeRcv or typeSdr is user and sender or receiver is equal to user id
        return $this->createQueryBuilder('t')
            ->where('t.typeRcv = :user')
            ->orWhere('t.typeSdr = :user')
            
            ->andWhere('t.idRcv = :userId')
            ->orWhere('t.idSdr = :userId')
            ->setParameter('user', 'user')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Transaction[] Returns an array of Transaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transaction
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
