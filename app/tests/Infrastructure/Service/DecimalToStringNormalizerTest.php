<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Service;

use App\Domain\Entity\Product;
use App\Infrastructure\Normalizer\DecimalToStringNormalizer;
use PHPUnit\Framework\TestCase;

class DecimalToStringNormalizerTest extends TestCase
{
    private DecimalToStringNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new DecimalToStringNormalizer();
    }

    public function testDenormalizeFormatsPriceAsString(): void
    {
        $data = [
            'price' => 123.456,
            'name' => 'Sample Product',
        ];

        $result = $this->normalizer->denormalize($data, Product::class);

        $this->assertIsFloat($result->getPrice());
        $this->assertEquals('123.46', $result->getPrice());
    }

    public function testDenormalizeHandlesPriceAsString(): void
    {
        $data = [
            'price' => '123.456',
            'name' => 'Sample Product',
        ];

        $result = $this->normalizer->denormalize($data, Product::class);

        $this->assertEquals('123.456', $result->getPrice());
    }

    public function testSupportsDenormalization(): void
    {
        $result = $this->normalizer->supportsDenormalization(['price' => 123.456], Product::class);
        $this->assertTrue($result);

        $result = $this->normalizer->supportsDenormalization(['price' => 123.456], 'SomeOtherType');
        $this->assertFalse($result);
    }

    public function testGetSupportedTypes(): void
    {
        $result = $this->normalizer->getSupportedTypes(null);
        $this->assertArrayHasKey(Product::class, $result);
        $this->assertTrue($result[Product::class]);
    }
}
