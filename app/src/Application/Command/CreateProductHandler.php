<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Entity\Product;
use App\Domain\Entity\ProductAttribute;
use App\Domain\Repository\AttributeRepositoryInterface;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\CurrencyRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Repository\ProductAttributeRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateProductHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private CurrencyRepositoryInterface $currencyRepository,
        private AttributeRepositoryInterface $attributeRepository,
        private ProductAttributeRepository $productAttributeRepository,
    ) {}

    public function __invoke(CreateProductCommand $command): void
    {
        $category = $this->categoryRepository->findById($command->categoryId);
        $currency = $this->currencyRepository->findById($command->currencyId);

        if (!$category || !$currency) {
            throw new \InvalidArgumentException('Invalid category or currency.');
        }

        $product = new Product(
            $command->name,
            $command->description,
            $command->price,
            $currency,
            $category
        );

        foreach ($command->attributes as $attr) {
            $attribute = $this->attributeRepository->findByCode($attr->getCode());

            if (null === $attribute) {
                throw new \InvalidArgumentException(sprintf('Attribute with code "%s" not found', $attr->getCode()));
            }

            $productAttribute = new ProductAttribute($product, $attribute, $attr->getCode());
            $this->productAttributeRepository->save($productAttribute);
        }

        $this->productRepository->save($product);
    }
}
