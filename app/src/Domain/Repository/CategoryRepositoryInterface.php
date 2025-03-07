<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;

    public function findByName(string $name): ?Category;
}
