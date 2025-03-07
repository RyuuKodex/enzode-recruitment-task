<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use Psr\Cache\CacheItemPoolInterface;

class RedisCacheService
{
    public function __construct(private CacheItemPoolInterface $productCache, private CacheItemPoolInterface $reportCache) {}

    public function setProductCache(string $key, string $value): void
    {
        $cacheItem = $this->productCache->getItem($key);
        $cacheItem->set($value);
        $this->productCache->save($cacheItem);
    }

    public function setReportCache(string $key, string $value): void
    {
        $cacheItem = $this->reportCache->getItem($key);
        $cacheItem->set($value);
        $this->reportCache->save($cacheItem);
    }

    public function getProductCache(string $key): mixed
    {
        $cacheItem = $this->productCache->getItem($key);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        return null;
    }

    public function getReportCache(string $key): mixed
    {
        $cacheItem = $this->reportCache->getItem($key);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        return null;
    }
}
