<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Entity\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvReportGenerator
{
    /**
     * @param Product[] $products
     */
    public function generateReport(array $products): StreamedResponse
    {
        if (empty($products)) {
            throw new \Exception('No products found for the report.');
        }

        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w');

            if (false === $handle) {
                throw new \Exception('Failed to open php://output for writing.');
            }

            fputcsv($handle, ['ID', 'Name', 'Description', 'Price', 'Currency', 'Category', 'Created At']);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->getId(),
                    $product->getName(),
                    $product->getDescription(),
                    number_format($product->getPrice(), 2, '.', ''),
                    $product->getCurrency()->getCode(),
                    $product->getCategory()->getName(),
                    $product->getCreatedAt()->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="products_report.csv"');

        return $response;
    }
}
