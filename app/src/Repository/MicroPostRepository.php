<?php

namespace App\Repository;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MicroPost>
 *
 * @method MicroPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MicroPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MicroPost[]    findAll()
 * @method MicroPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroPostRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    /**
     * @param MicroPost $entity
     * @param bool $flush
     * @return void
     */
    public function add(MicroPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param MicroPost $entity
     * @param bool $flush
     * @return void
     */
    public function remove(MicroPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array
     */
    public function findAllWithComments(): array
    {
        return $this->findAllQuery(withComments: true)->getQuery()->getResult();
    }

    public function findAllByAuthor(int|User $author): array{
        return $this->findAllQuery(
            withComments: true,
            withLikes: true,
            withAuthors: true,
            withProfiles: true
        )->where('p.author = :author')
            ->setParameter(
                'author',
                $author
            )->getQuery()
            ->getResult();
    }

    private function findAllQuery(
        bool $withComments = false,
        bool $withLikes = false,
        bool $withAuthors = false,
        bool $withProfiles = false
    ): QueryBuilder {
        $query = $this->createQueryBuilder('p');

        if ($withComments) {
            $query->leftJoin('p.comments', 'c')
                ->addSelect('c');
        }

        if ($withLikes) {
            $query->leftJoin('p.likedBy', 'l')
                ->addSelect('l');
        }

        if ($withAuthors || $withProfiles) {
            $query->leftJoin('p.author', 'a')
                ->addSelect('a');
        }

        if ($withProfiles) {
            $query->leftJoin('a.userProfile', 'up')
                ->addSelect('up');
        }

        return $query->orderBy('p.createdAt', 'DESC');
    }
}
