<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * @return Recipe[] Returns an array of Recipe objects
     */
    public function findByDurationLowerThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'c')
            ->where('r.duration <= :duration')
            ->orderBy('r.duration', 'ASC')
            ->leftJoin('r.category', 'c')
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult()
        ;
    }

    public function paginateRecipes(int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->select('r', 'c');

        if ($userId) {
            $builder = $builder->andWhere('r.user = :userId')
                ->setParameter('userId', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            10,
            [
                'distinct' => true,
                'defaultSortFieldName' => 'r.createdAt',
                'defaultSortDirection' => 'DESC',
                'defaultFilterFields' => [
                    'r.title',
                    'r.description',
                    'c.id',
                    'r.createdAt',
                    'r.updatedAt',
                ],
            ]
        );
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
