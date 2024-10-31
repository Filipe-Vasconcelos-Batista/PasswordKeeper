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
    public function getAuth(string $name, int $userId): bool{
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
    public function setSecret(string $secret, int $userId): void
    {
            $item = $this -> $cache->getItem('user_' . $userId);
            $item->expiresAfter(300);
            $item->set(['secret' => $secret]);
    }
    public function getSecret(int $userId): ?string
    {
        $cacheItem = $this -> $cache->getItem('user_' . $userId);
        $cacheData=$cacheItem->isHit() ? $cacheItem->get() : null;
        if($cacheData !== null){
            return $cacheItem['secret'];
        }
        return null;

    }

}

