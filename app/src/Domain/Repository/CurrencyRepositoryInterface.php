<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Currency;

interface CurrencyRepositoryInterface
{
    public function findById(int $id): ?Currency;

    public function findByCode(string $code): ?Currency;
}
