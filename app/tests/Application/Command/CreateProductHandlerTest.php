<?php

declare(strict_types=1);

namespace App\Tests\Application\Command;

use App\Application\Command\CreateProductCommand;
use App\Application\Command\CreateProductHandler;
use App\Domain\Entity\Attribute;
use App\Domain\Entity\Category;
use App\Domain\Entity\Currency;
use App\Domain\Repository\AttributeRepositoryInterface;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\CurrencyRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Repository\ProductAttributeRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateProductHandlerTest extends TestCase
{
    private CreateProductHandler $handler;
    private MockObject $productRepository;
    private MockObject $categoryRepository;
    private MockObject $currencyRepository;
    private MockObject $attributeRepository;
    private MockObject $productAttributeRepository;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->currencyRepository = $this->createMock(CurrencyRepositoryInterface::class);
        $this->attributeRepository = $this->createMock(AttributeRepositoryInterface::class);
        $this->productAttributeRepository = $this->createMock(ProductAttributeRepository::class);

        $this->handler = new CreateProductHandler(
            $this->productRepository,
            $this->categoryRepository,
            $this->currencyRepository,
            $this->attributeRepository,
            $this->productAttributeRepository
        );
    }

    public function testInvokeCreatesProductSuccessfully(): void
    {
        $categoryId = 1;
        $currencyId = 1;
        $productName = 'Test Product';
        $productDescription = 'Test Description';
        $productPrice = 100;

        $attributes = [$this->createMock(Attribute::class)];

        $command = new CreateProductCommand(
            $productName,
            $productDescription,
            $productPrice,
            $currencyId,
            $categoryId,
            $attributes
        );

        $category = $this->createMock(Category::class);
        $currency = $this->createMock(Currency::class);

        $this->categoryRepository->method('findById')->with($categoryId)->willReturn($category);
        $this->currencyRepository->method('findById')->with($currencyId)->willReturn($currency);

        $attribute = $this->createMock(Attribute::class); // Now we return an instance of Attribute
        $this->attributeRepository->method('findByCode')->with('color')->willReturn($attribute);

        $this->productRepository->expects($this->once())->method('save');
        $this->productAttributeRepository->expects($this->once())->method('save');

        $this->handler->__invoke($command);
    }

    public function testInvokeThrowsExceptionWhenCategoryOrCurrencyIsInvalid(): void
    {
        $categoryId = 1;
        $currencyId = 1;
        $productName = 'Test Product';
        $productDescription = 'Test Description';
        $productPrice = 100;

        $attributes = [$this->createMock(Attribute::class)];

        $command = new CreateProductCommand(
            $productName,
            $productDescription,
            $productPrice,
            $currencyId,
            $categoryId,
            $attributes
        );

        $this->categoryRepository->method('findById')->with($categoryId)->willReturn(null);
        $this->currencyRepository->method('findById')->with($currencyId)->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid category or currency.');

        $this->handler->__invoke($command);
    }
}
