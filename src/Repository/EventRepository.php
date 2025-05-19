<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Trouver les événements par filtres
     */
    public function findByFilters(?string $category = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $qb = $this->createQueryBuilder('e');

        // Filtre par catégorie
        if ($category) {
            $qb->andWhere('e.categorie = :category')
                ->setParameter('category', $category);
        }

        // Filtre par date de début
        if ($startDate) {
            $qb->andWhere('e.date >= :startDate')
                ->setParameter('startDate', new \DateTime($startDate));
        }

        // Filtre par date de fin
        if ($endDate) {
            $qb->andWhere('e.date <= :endDate')
                ->setParameter('endDate', new \DateTime($endDate));
        }

        return $qb->orderBy('e.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver toutes les catégories distinctes
     */
    public function findAllCategories(): array
    {
        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.categorie')
            ->orderBy('e.categorie', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Compter les événements à venir
     */
    public function countUpcomingEvents(): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.date >= :today')
            ->setParameter('today', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compter les événements par catégorie
     */
    public function countByCategory(): array
    {
        $categories = $this->findAllCategories();
        $result = [];

        foreach ($categories as $category) {
            $count = $this->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.categorie = :category')
                ->setParameter('category', $category)
                ->getQuery()
                ->getSingleScalarResult();

            $result[] = [
                'category' => $category,
                'count' => (int) $count,
            ];
        }

        return $result;
    }
}