<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Service\CsvReportGenerator;
use App\Infrastructure\Service\RedisCacheService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetProductReportHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CsvReportGenerator $csvReportGenerator,
        private RedisCacheService $redisCacheService,
    ) {}

    public function __invoke(GetProductReportQuery $query): StreamedResponse
    {
        $cacheKey = 'product_report_'.md5(serialize($query));

        $cachedData = $this->redisCacheService->getReportCache($cacheKey);

        $products = null !== $cachedData ? unserialize($cachedData) : null;

        if (null === $products) {
            $productArray = $this->productRepository->findByFilters(
                $query->name,
                $query->categoryId,
                $query->priceMin,
                $query->priceMax
            );

            $this->redisCacheService->setReportCache($cacheKey, serialize($productArray));

            $products = $productArray;
        }

        return $this->csvReportGenerator->generateReport($products);
    }
}
