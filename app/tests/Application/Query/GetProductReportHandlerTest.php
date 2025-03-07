<?php

declare(strict_types=1);

namespace App\Tests\Application\Query;

use App\Application\Query\GetProductReportHandler;
use App\Application\Query\GetProductReportQuery;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Service\CsvReportGenerator;
use App\Infrastructure\Service\RedisCacheService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GetProductReportHandlerTest extends TestCase
{
    private GetProductReportHandler $handler;
    private MockObject $productRepository;
    private MockObject $csvReportGenerator;
    private MockObject $redisCacheService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->csvReportGenerator = $this->createMock(CsvReportGenerator::class);
        $this->redisCacheService = $this->createMock(RedisCacheService::class);

        $this->handler = new GetProductReportHandler(
            $this->productRepository,
            $this->csvReportGenerator,
            $this->redisCacheService
        );
    }

    public function testInvokeCacheHit(): void
    {
        $query = new GetProductReportQuery('product name', 1, 100, 200);

        $cachedData = serialize(['data']);
        $this->redisCacheService->method('getReportCache')->willReturn($cachedData);

        $this->productRepository->expects($this->never())->method('findByFilters');

        $this->csvReportGenerator->expects($this->once())->method('generateReport')
            ->with($this->isType('array'))
            ->willReturn(new StreamedResponse())
        ;

        $response = $this->handler->__invoke($query);

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    public function testInvokeCacheMiss(): void
    {
        $query = new GetProductReportQuery('product name', 1, 100, 200);

        $this->redisCacheService->method('getReportCache')->willReturn(null);

        $productArray = ['data'];
        $this->productRepository->method('findByFilters')->willReturn($productArray);

        $this->redisCacheService->expects($this->once())->method('setReportCache');

        $this->csvReportGenerator->expects($this->once())->method('generateReport')
            ->with($productArray)
            ->willReturn(new StreamedResponse())
        ;

        $response = $this->handler->__invoke($query);

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }
}
