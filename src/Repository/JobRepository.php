<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * Find next new job, taking into account other instances
     * @return Job
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getNextJob()
    {
        $entityManager = $this->getEntityManager();

        if (!$entityManager->getConnection()->beginTransaction()) {
            throw new \RuntimeException("Can't begin transaction");
        }

        $query = $this->getEntityManager()->createQuery(
            "SELECT job FROM \App\Entity\Job job WHERE job.status = :status" //SKIP LOCKED for Mysql 8.0
        )
            ->setParameter("status", Job::STATUS_NEW)
            ->setMaxResults(1)
            ->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);

        if ($job = $query->getOneOrNullResult()) {
            $job->setStatus(Job::STATUS_PROCESSING);
            $entityManager->flush();
        }

        $entityManager->getConnection()->commit();

        return $job;
    }

    public function saveJob(Job $job): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($job);
        $entityManager->flush();
    }

    // /**
    //  * @return Job[] Returns an array of Job objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Job
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
