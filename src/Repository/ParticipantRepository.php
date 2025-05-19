<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participant>
 */
class ParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }
    public function countEventsByMemberId(int $memberId): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT p.event_id)')
            ->andWhere('p.member_id = :memberId')
            ->setParameter('memberId', $memberId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findEventsByMemberId(int $memberId): array
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT e
         FROM App\Entity\Event e
         JOIN App\Entity\Participant p WITH p.event_id = e.id
         WHERE p.member_id = :memberId'
        )
            ->setParameter('memberId', $memberId)
            ->getResult();
    }

    //    /**
    //     * @return Participant[] Returns an array of Participant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Participant
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
