<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;

    /**
     * @return Product[]
     */
    public function findByFilters(?string $name, ?int $categoryId, ?float $priceMin, ?float $priceMax): array;
}
