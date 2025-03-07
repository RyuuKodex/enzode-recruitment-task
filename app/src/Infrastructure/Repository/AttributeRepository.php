<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Attribute;
use App\Domain\Repository\AttributeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attribute>
 */
class AttributeRepository extends ServiceEntityRepository implements AttributeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    public function findById(int $id): ?Attribute
    {
        return $this->find($id);
    }

    public function findByCode(string $code): ?Attribute
    {
        return $this->findOneBy(['code' => $code]);
    }
}
