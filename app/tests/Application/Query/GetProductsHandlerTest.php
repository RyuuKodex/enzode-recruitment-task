<?php

declare(strict_types=1);

namespace App\Tests\Application\Query;

use App\Application\Query\GetProductsHandler;
use App\Application\Query\GetProductsQuery;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Service\RedisCacheService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetProductsHandlerTest extends TestCase
{
    private GetProductsHandler $handler;
    private MockObject $productRepository;
    private MockObject $redisCacheService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->redisCacheService = $this->createMock(RedisCacheService::class);

        $this->handler = new GetProductsHandler(
            $this->productRepository,
            $this->redisCacheService
        );
    }

    public function testHandleCacheHit(): void
    {
        $query = new GetProductsQuery('product-name', 1, 100, 1000);
        $cacheKey = 'products_'.md5(serialize($query));

        $product1 = $this->createMock(Product::class);
        $product2 = $this->createMock(Product::class);

        $cachedData = serialize([$product1, $product2]);

        $this->redisCacheService->expects($this->once())
            ->method('getProductCache')
            ->with($cacheKey)
            ->willReturn($cachedData)
        ;

        $products = $this->handler->__invoke($query);

        $this->assertCount(2, $products);
    }

    public function testHandleCacheMiss(): void
    {
        $query = new GetProductsQuery('product-name', 1, 100, 1000);
        $cacheKey = 'products_'.md5(serialize($query));

        $this->redisCacheService->expects($this->once())
            ->method('getProductCache')
            ->with($cacheKey)
            ->willReturn(null)
        ;

        $this->productRepository->expects($this->once())
            ->method('findByFilters')
            ->with('product-name', 1, 100, 1000)
            ->willReturn([
                $this->createMock(Product::class),
                $this->createMock(Product::class),
            ])
        ;

        $this->redisCacheService->expects($this->once())
            ->method('setProductCache')
            ->with($cacheKey, $this->isType('string'))
        ;

        $products = $this->handler->__invoke($query);

        $this->assertCount(2, $products);
    }

    public function testInvokeEmptyCacheAndNoResults(): void
    {
        $query = new GetProductsQuery('nonexistent product', 999, 1000, 2000);

        $this->redisCacheService->method('getProductCache')->willReturn(null);
        $this->productRepository->method('findByFilters')->willReturn([]);

        $products = $this->handler->__invoke($query);

        $this->assertIsArray($products);
        $this->assertEmpty($products);
    }
}
