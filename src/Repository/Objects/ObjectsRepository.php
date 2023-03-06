<?php

namespace App\Repository\Objects;

use App\Data\SearchData;
use App\Entity\Objects\Objects;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Objects|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objects|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objects[]    findAll()
 * @method Objects[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class ObjectsRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Objects::class);

        $this->paginator = $paginator;
    }

    public function findAllNoDeleted()
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    /*Count Request*/
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countObjects() {
        return $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->where('o.deletedAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countWhereIsLocated($expositionLocation) {
        return $this->createQueryBuilder('o')
            ->where('o.expositionLocation = :expositionLocation')
            ->andWhere('o.deletedAt IS NULL')
            ->select('count(o.expositionLocation)')
            ->setParameter('expositionLocation', $expositionLocation)
            ->getQuery()
            ->getSingleScalarResult();
    }
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countToFix() {
        return $this->createQueryBuilder('o')
            ->where('o.deletedAt IS NULL')
            ->andWhere('o.state = 4')
            ->select('count(o.state)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    /*Find Request*/
    public function findLatest() {
        return $this->createQueryBuilder('o')
            ->where('o.deletedAt IS NULL')
            ->join('o.tags', 't')
            ->select('o, t')
            //DeletedAt
            ->getQuery();
    }

    public function findMostViewed(): PaginationInterface
    {
        $query = $this->createQueryBuilder('o')
            ->where('o.deletedAt IS NULL')
            ->select('o')
            ->getQuery();
        return $this->paginator->paginate(
            $query,
            1,
            8
        );
    }

    public function findDeletedObjects(): PaginationInterface
    {
        $query = $this->createQueryBuilder('o')
            ->where('o.deletedAt IS NOT NULL')
            ->select('o')
            ->getQuery();
        return $this->paginator->paginate(
            $query,
            1,
            40
        );
    }

    /////////SEARCH////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Find/search articles by title/content
    public function searchObjects(SearchData $searchData)
    {
        $query = $this
            ->createQueryBuilder('o')
            ->leftjoin('o.vernacularName', 'vernaName')
            ->leftjoin('o.typology', 'typo')
            ->leftjoin('o.gods', 'g')
            ->leftjoin('o.population', 'pop')
            ->leftjoin('o.origin', 'ori')
            ->leftjoin('o.materials', 'mat')

        ;

        $query = $query
            ->orWhere('o.code LIKE :q')
            ->orWhere('vernaName.name LIKE :q')
            ->orWhere('typo.name LIKE :q')
            ->orWhere('g.name LIKE :q')
            ->orWhere('pop.name LIKE :q')
            ->orWhere('ori.name LIKE :q')
            ->orWhere('mat.name LIKE :q')
//                ->orWhere('o.usageFonction LIKE :q')
//                ->orWhere('o.historicDate = :q')
//                ->orWhere('o.historicDetail LIKE :q')
//                ->orWhere('o.usageFonction LIKE :q')
//                ->orWhere('o.stateCommentary LIKE :q')
//                ->orWhere('o.state LIKE :q')
//                ->orWhere('o.memo LIKE :q')
            ->setParameter('q', "%{$searchData->q}%");


//        if(!empty($searchData->updatedBy)) {
//            $query = $query
//                ->select('user', 'u')
//                ->orWhere('u.')
//                ->andWhere('u.updatedBy == o.updatedBy')
//                ->setParameter('updatedBy', "%{$searchData->updatedBy}%");
//        }
//
        if(!empty($searchData->isBasemented)) {
            $query = $query
                ->andWhere('o.isBasemented = 1');
        }
        if(!empty($searchData->expositionLocation)) {
            $query = $query
                ->select('expositionLocation', 'o')
                ->join('o.expositionLocation', 'expositionLocation')
                ->andWhere('expositionLocation.id IN (:expositionLocation)')
                ->setParameter('expositionLocation', $searchData->expositionLocation);
        }
        if(!empty($searchData->vernacularName)) {
            $query = $query
                ->select('vernacularName', 'o')
                ->join('o.vernacularName', 'vernacularName')
                ->andWhere('vernacularName.id IN (:vernacularName)')
                ->setParameter('vernacularName', $searchData->vernacularName);
        }
        if(!empty($searchData->typology)) {
            $query = $query
                ->select('typology', 'o')
                ->join('o.typology', 'typology')
                ->andWhere('typology.id IN (:typology)')
                ->setParameter('typology', $searchData->typology);
        }
        if(!empty($searchData->gods)) {
            $query = $query
                ->select('gods', 'o')
                ->join('o.gods', 'gods')
                ->andWhere('gods.id IN (:gods)')
                ->setParameter('gods', $searchData->gods);
        }
        if(!empty($searchData->relatedGods)) {
            $query = $query
                ->select('relatedGods', 'o')
                ->join('o.relatedGods', 'relatedGods')
                ->orWhere('relatedGods.id IN (:relatedGods)')
                ->setParameter('relatedGods', $searchData->relatedGods);
        }
        if(!empty($searchData->origin)) {
            $query = $query
                ->select('origin', 'o')
                ->join('o.origin', 'origin')
                ->orWhere('origin.id IN (:origin)')
                ->setParameter('origin', $searchData->origin);
        }
        if(!empty($searchData->population)) {
            $query = $query
                ->select('population', 'o')
                ->join('o.population', 'population')
                ->orWhere('population.id IN (:population)')
                ->setParameter('population', $searchData->population);
        }
        if(!empty($searchData->materials)) {
            $query = $query
                ->select('materials', 'o')
                ->join('o.materials', 'materials')
                ->orWhere('materials.id IN (:materials)')
                ->setParameter('materials', $searchData->materials);
        }
//        if(!empty($searchData->state)) {
//            $query = $query
//                ->select('state', 'o')
//                ->join('o.state', 'state')
//                ->andWhere('state.id IN (:state)')
//                ->setParameter('state', $searchData->state);
//        }
//        if(!empty($searchData->floor)) {
//            $query = $query
//                ->select('floor', 'o')
//                ->join('o.floor', 'floor')
//                ->andWhere('floor.id IN (:floor)')
//                ->setParameter('floor', $searchData->floor);
//        }
//        if(!empty($searchData->showcaseCode)) {
//            $query = $query
//                ->andWhere('o.showcaseCode LIKE :showcaseCode')
//                ->setParameter('showcaseCode', $searchData->showcaseCode);
//        }
//        if(!empty($searchData->shelf)) {
//            $query = $query
//                ->andWhere('o.shelf LIKE :shelf')
//                ->setParameter('shelf', $searchData->shelf);
//        }


        //SORT
        if(!empty($searchData->isSortNumeric)) {
            $query = $query
                ->orderBy('o.code', 'ASC');
        }
        if(!empty($searchData->isSortNumericReverse)) {
            $query = $query
                ->orderBy('o.code', 'DESC');
        }
        if(!empty($searchData->isSortAlpha)) {
            $query = $query
                ->orderBy('o.vernacularName', 'ASC');
        }
        if(!empty($searchData->isSortAlphaReverse)) {
            $query = $query
                ->orderBy('o.vernacularName', 'DESC');
        }
        if(!empty($searchData->sortDateUpdate)) {
            $query = $query
                ->orderBy('o.updatedAt', 'DESC');
        }


        $query = $query->andWhere('o.deletedAt IS NULL');
        $query = $query->getQuery();

        return $query->getResult();

//        return $this->paginator->paginate(
//            $query,
//            $searchData->page,
//            8
//        );
    }


}
