<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 2000,
        maxMessage: 'Description cannot exceed {{ limit }} characters.'
    )]
    private ?string $description;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Price is required.')]
    #[Assert\Positive(message: 'Price must be a positive value.')]
    private float $price;

    #[ORM\ManyToOne(targetEntity: Currency::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Currency must be selected.')]
    private Currency $currency;

    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Category must be selected.')]
    private Category $category;

    /**
     * @var Collection<int, ProductAttribute>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductAttribute::class, cascade: ['persist', 'remove'])]
    private Collection $attributes;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull(message: 'Created At date is required.')]
    #[Assert\Type('\DateTimeImmutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $name, ?string $description, float $price, Currency $currency, Category $category)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->currency = $currency;
        $this->category = $category;
        $this->attributes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function addAttribute(ProductAttribute $attribute): void
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
        }
    }
}
