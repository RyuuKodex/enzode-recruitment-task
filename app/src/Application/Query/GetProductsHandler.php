<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\DTO\ProductDTO;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Service\RedisCacheService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetProductsHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private RedisCacheService $redisCacheService
    ) {}

    /**
     * @return ProductDTO[]
     */
    public function __invoke(GetProductsQuery $query): array
    {
        $cacheKey = 'products_'.md5(serialize($query));

        $cachedData = $this->redisCacheService->getProductCache($cacheKey);
        $products = null !== $cachedData ? unserialize($cachedData) : null;

        if (null === $products) {
            $productArray = $this->productRepository->findByFilters(
                $query->name,
                $query->categoryId,
                $query->priceMin,
                $query->priceMax
            );

            $products = array_map(fn ($product) => ProductDTO::fromEntity($product), $productArray);

            $this->redisCacheService->setProductCache($cacheKey, serialize($productArray));
        } else {
            $products = array_map(fn ($product) => ProductDTO::fromEntity($product), $products);
        }

        return $products;
    }
}
