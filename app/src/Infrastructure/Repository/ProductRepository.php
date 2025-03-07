<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $product): void
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }

    /**
     * @return Product[]
     */
    public function findByFilters(?string $name, ?int $categoryId, ?float $priceMin, ?float $priceMax): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.currency', 'cu')
            ->addSelect('c', 'cu')
        ;

        if ($name) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', "%{$name}%")
            ;
        }
        if ($categoryId) {
            $qb->andWhere('p.category = :categoryId')
                ->setParameter('categoryId', $categoryId)
            ;
        }
        if ($priceMin) {
            $qb->andWhere('p.price >= :priceMin')
                ->setParameter('priceMin', $priceMin)
            ;
        }
        if ($priceMax) {
            $qb->andWhere('p.price <= :priceMax')
                ->setParameter('priceMax', $priceMax)
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
