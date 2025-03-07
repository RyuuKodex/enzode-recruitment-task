<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Attribute;

interface AttributeRepositoryInterface
{
    public function findById(int $id): ?Attribute;

    public function findByCode(string $code): ?Attribute;
}
