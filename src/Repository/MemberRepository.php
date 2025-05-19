<?php

namespace App\Repository;

use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Member>
 *
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Member) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Trouver les membres par filtres
     */
    public function findByFilters(?string $role = null, ?string $startDate = null, ?string $endDate = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('m');

        // Filtre par rôle
        if ($role) {
            $qb->andWhere('m.roles LIKE :role')
                ->setParameter('role', '%"'.$role.'"%');
        }

        // Filtre par date de début d'adhésion
        if ($startDate) {
            $qb->andWhere('m.membershipStartDate >= :startDate')
                ->setParameter('startDate', new \DateTime($startDate));
        }

        // Filtre par date de fin d'adhésion
        if ($endDate) {
            $qb->andWhere('m.membershipEndDate <= :endDate')
                ->setParameter('endDate', new \DateTime($endDate));
        }

        // Filtre par statut d'adhésion
        if ($status) {
            $qb->andWhere('m.membershipStatus = :status')
                ->setParameter('status', $status);
        }

        return $qb->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver tous les rôles distincts
     */
    public function findAllRoles(): array
    {
        $members = $this->findAll();
        $roles = [];

        foreach ($members as $member) {
            foreach ($member->getRoles() as $role) {
                if ($role !== 'ROLE_USER' && !in_array($role, $roles)) {
                    $roles[] = $role;
                }
            }
        }

        return $roles;
    }

    /**
     * Compter les membres par rôle
     */
    public function countByRole(): array
    {
        $roles = $this->findAllRoles();
        $result = [];

        foreach ($roles as $role) {
            $count = $this->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.roles LIKE :role')
                ->setParameter('role', '%"'.$role.'"%')
                ->getQuery()
                ->getSingleScalarResult();

            $result[] = [
                'role' => str_replace('ROLE_', '', $role),
                'count' => (int) $count,
            ];
        }

        return $result;
    }
}