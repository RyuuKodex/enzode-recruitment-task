<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Entity\Attribute;

final readonly class CreateProductCommand
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $currencyId,
        public int $categoryId,
        /**
         * @var Attribute[]
         */
        public array $attributes
    ) {}
}
