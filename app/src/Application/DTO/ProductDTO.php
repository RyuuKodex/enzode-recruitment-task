<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\Product;

class ProductDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public float $price,
        public string $currency,
        public string $category
    ) {}

    public static function fromEntity(Product $product): self
    {
        return new self(
            $product->getId(),
            $product->getName(),
            $product->getDescription(),
            $product->getPrice(),
            $product->getCurrency()->getCode(),
            $product->getCategory()->getName()
        );
    }
}
