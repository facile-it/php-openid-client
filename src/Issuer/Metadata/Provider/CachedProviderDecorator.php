<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata\Provider;

use function is_array;
use function json_decode;
use function json_encode;
use Psr\SimpleCache\CacheInterface;
use function sha1;
use function substr;

final class CachedProviderDecorator implements RemoteProviderInterface
{
    /** @var RemoteProviderInterface */
    private $provider;

    /** @var CacheInterface */
    private $cache;

    /** @var null|int */
    private $cacheTtl;

    /**
     * @var callable
     * @phpstan-var callable(string): string
     */
    private $cacheIdGenerator;

    public function __construct(
        RemoteProviderInterface $provider,
        CacheInterface $cache,
        ?int $cacheTtl = null,
        ?callable $cacheIdGenerator = null
    ) {
        $this->provider = $provider;
        $this->cache = $cache;
        $this->cacheTtl = $cacheTtl;
        $this->cacheIdGenerator = $cacheIdGenerator ?? static function (string $uri): string {
            return substr(sha1($uri), 0, 65);
        };
    }

    public function fetch(string $uri): array
    {
        $cacheId = ($this->cacheIdGenerator)($uri);

        if (is_array($data = json_decode($this->cache->get($cacheId) ?? '', true))) {
            return $data;
        }

        $data = $this->provider->fetch($uri);

        $this->cache->set($cacheId, json_encode($data), $this->cacheTtl);

        return $data;
    }

    public function isAllowedUri(string $uri): bool
    {
        return $this->provider->isAllowedUri($uri);
    }
}
