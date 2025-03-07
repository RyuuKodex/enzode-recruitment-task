<?php

declare(strict_types=1);

namespace App\Infrastructure\Normalizer;

use App\Domain\Entity\Product;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DecimalToStringNormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (isset($data['price']) && is_float($data['price'])) {
            $data['price'] = number_format($data['price'], 2, '.', '');
        }

        return $data;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return Product::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Product::class => true,
        ];
    }
}
