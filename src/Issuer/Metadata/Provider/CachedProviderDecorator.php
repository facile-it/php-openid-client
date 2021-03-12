<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata\Provider;

use Psr\SimpleCache\CacheInterface;

use function is_array;
use function json_decode;
use function json_encode;
use function sha1;

final class CachedProviderDecorator implements RemoteProviderInterface
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var callable
     * @psalm-var callable(string): string
     */
    private $cacheIdGenerator;

    /**
     * @var int|null
     */
    private $cacheTtl;

    /**
     * @var RemoteProviderInterface
     */
    private $provider;

    /**
     * @psalm-param null|callable(string): string $cacheIdGenerator
     */
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
            return mb_substr(sha1($uri), 0, 65);
        };
    }

    public function fetch(string $uri): array
    {
        $cacheId = ($this->cacheIdGenerator)($uri);

        /** @var string $cached */
        $cached = $this->cache->get($cacheId) ?? '';
        /** @var array<mixed>|string|null $data */
        $data = json_decode($cached, true);

        if (is_array($data)) {
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
