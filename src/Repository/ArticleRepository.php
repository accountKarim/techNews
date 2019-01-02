<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{

    const MAX_RESULT = 5;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function FindBySpotlight() {
        return $this->createQueryBuilder('a')
            ->where('a.spotlight = 1')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(self::MAX_RESULT)
            ->getQuery()
            ->getResult();
    }
}
