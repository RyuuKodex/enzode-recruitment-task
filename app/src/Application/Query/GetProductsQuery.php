<?php

declare(strict_types=1);

namespace App\Application\Query;

final readonly class GetProductsQuery
{
    public function __construct(
        public ?string $name = null,
        public ?int $categoryId = null,
        public ?float $priceMin = null,
        public ?float $priceMax = null
    ) {}
}
