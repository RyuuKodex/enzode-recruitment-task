<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Currency;
use App\Domain\Repository\CurrencyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Currency>
 */
class CurrencyRepository extends ServiceEntityRepository implements CurrencyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    public function findById(int $id): ?Currency
    {
        return $this->find($id);
    }

    public function findByCode(string $code): ?Currency
    {
        return $this->findOneBy(['code' => strtoupper($code)]);
    }
}
