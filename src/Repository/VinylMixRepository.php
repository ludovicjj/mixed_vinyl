<?php

namespace App\Repository;

use App\Entity\VinylMix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VinylMix>
 *
 * @method VinylMix|null find($id, $lockMode = null, $lockVersion = null)
 * @method VinylMix|null findOneBy(array $criteria, array $orderBy = null)
 * @method VinylMix[]    findAll()
 * @method VinylMix[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VinylMixRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VinylMix::class);
    }

    public function save(VinylMix $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VinylMix $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllGreaterThanVote(int $vote)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT v
            FROM App\Entity\VinylMix v
            WHERE v.votes > :vote
            ORDER BY v.votes ASC
            '
        )->setParameter('vote', $vote);

        // Return an array of VinylMix Object
        return $query->getResult(1);
    }

    public function findAllGreaterThanVoteSQL(int $vote): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM vinyl_mix v
            WHERE v.votes > :vote
            ORDER BY v.votes ASC
        ';

        $statement = $connection->prepare($sql);
        $result = $statement->executeQuery(['vote' => $vote]);

        // returns an array of arrays
        return $result->fetchAllAssociative();
    }


    public function createOrderByVotesQueryBuilder(string $genre = null): QueryBuilder
    {
        $queryBuilder = $this->addOrderByVotesQueryBuilder();
        if ($genre) {
            $queryBuilder->andWhere('mix.genre = :genre')
                ->setParameter('genre', $genre);
        }

        return $queryBuilder;
    }

    public function addOrderByVotesPaginator(string $genre = null, int $currentPage, int $maxPerPage): Paginator
    {
        $queryBuilder = $this->createOrderByVotesQueryBuilder($genre);
        $queryBuilder
            ->setFirstResult(($currentPage - 1) * $maxPerPage)
            ->setMaxResults($maxPerPage);

        return new Paginator($queryBuilder);
    }

    private function addOrderByVotesQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('mix');
        return $queryBuilder->addOrderBy('mix.votes', 'DESC');
    }

//    public function findOneBySomeField($value): ?VinylMix
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
