<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_attributes')]
class ProductAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Attribute::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Attribute $attribute;

    #[ORM\Column(type: 'string', length: 255)]
    private string $value;

    public function __construct(Product $product, Attribute $attribute, string $value)
    {
        $this->product = $product;
        $this->attribute = $attribute;
        $this->value = $value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
