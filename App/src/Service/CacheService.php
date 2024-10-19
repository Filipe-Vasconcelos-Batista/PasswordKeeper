<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;

class CacheService
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache=$cache;
    }
    public function checkAuth(string $name, int $userId): bool{
        $cacheItem= $this->cache->getItem('user_' . $userId);
        $cacheData=$cacheItem->isHit() ? $cacheItem->get() : false;
        return isset($cacheData[$name]) && $cacheData[$name]===true;
    }
    public function setAuth(string $name,int $userId): void
    {
        $item = $this -> $cache->getItem('user_' . $userId);
        $item->expiresAfter(3600);
        $item->set([$name => true]);
        $cache->save($item);
    }

}

