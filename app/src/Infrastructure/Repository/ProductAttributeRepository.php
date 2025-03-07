<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\ProductAttribute;
use App\Domain\Repository\ProductAttributeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductAttribute>
 */
class ProductAttributeRepository extends ServiceEntityRepository implements ProductAttributeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductAttribute::class);
    }

    public function save(ProductAttribute $productAttribute): void
    {
        $this->_em->persist($productAttribute);
        $this->_em->flush();
    }

    public function findByProductId(int $productId): array
    {
        return $this->findBy(['product' => $productId]);
    }
}
