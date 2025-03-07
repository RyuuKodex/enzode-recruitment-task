<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\ProductAttribute;

interface ProductAttributeRepositoryInterface
{
    public function save(ProductAttribute $productAttribute): void;

    /**
     * @return ProductAttribute[]
     */
    public function findByProductId(int $productId): array;
}
