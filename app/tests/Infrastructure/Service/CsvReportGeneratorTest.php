<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Service;

use App\Domain\Entity\Category;
use App\Domain\Entity\Currency;
use App\Domain\Entity\Product;
use App\Infrastructure\Service\CsvReportGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvReportGeneratorTest extends TestCase
{
    private CsvReportGenerator $csvReportGenerator;

    protected function setUp(): void
    {
        $this->csvReportGenerator = new CsvReportGenerator();
    }

    public function testGenerateReportWithProducts(): void
    {
        $currencyMock = $this->createMock(Currency::class);
        $currencyMock->method('getCode')->willReturn('USD');

        $categoryMock = $this->createMock(Category::class);
        $categoryMock->method('getName')->willReturn('Electronics');

        $productMock = $this->createMock(Product::class);
        $productMock->method('getId')->willReturn(1);
        $productMock->method('getName')->willReturn('Product 1');
        $productMock->method('getDescription')->willReturn('Description 1');
        $productMock->method('getPrice')->willReturn(100.50);
        $productMock->method('getCurrency')->willReturn($currencyMock);
        $productMock->method('getCategory')->willReturn($categoryMock);
        $productMock->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2022-03-06 14:30:00'));

        $products = [$productMock];

        $response = $this->csvReportGenerator->generateReport($products);

        $this->assertInstanceOf(StreamedResponse::class, $response);

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename="products_report.csv"', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->send();
        $csvContent = ob_get_clean();

        $expectedCsv = 'ID,Name,Description,Price,Currency,Category,"Created At"'."\n"
            .'1,"Product 1","Description 1",100.50,USD,Electronics,"2022-03-06 14:30:00"'."\n";

        $this->assertEquals($expectedCsv, $csvContent);
    }

    public function testGenerateReportThrowsExceptionForEmptyProducts(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No products found for the report.');

        $this->csvReportGenerator->generateReport([]);
    }
}
