<?php

namespace App\Repository\Objects\SharedBookmarks;

use App\Entity\Objects\SharedBookmarks\SharedBookmarks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SharedBookmarks>
 *
 * @method SharedBookmarks|null find($id, $lockMode = null, $lockVersion = null)
 * @method SharedBookmarks|null findOneBy(array $criteria, array $orderBy = null)
 * @method SharedBookmarks[]    findAll()
 * @method SharedBookmarks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SharedBookmarksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SharedBookmarks::class);
    }

    public function save(SharedBookmarks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SharedBookmarks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SharedBookmarks[] Returns an array of SharedBookmarks objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SharedBookmarks
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
