<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Issuer\Metadata\Provider;

use Facile\OpenIDClient\Issuer\Metadata\Provider\CachedProviderDecorator;
use Facile\OpenIDClient\Issuer\Metadata\Provider\RemoteProviderInterface;
use Facile\OpenIDClientTest\TestCase;
use Prophecy\Argument;
use Psr\SimpleCache\CacheInterface;

/**
 * @internal
 * @coversNothing
 */
final class CachedProviderDecoratorTest extends TestCase
{
    public function testShouldPersistCachedData(): void
    {
        $uri = 'https://example.com';
        $provider1 = $this->prophesize(RemoteProviderInterface::class);
        $cache = $this->prophesize(CacheInterface::class);

        $metadata = [
            'foo1' => 'bar1',
        ];
        $provider1->fetch($uri)->shouldBeCalled()->willReturn($metadata);

        $provider = new CachedProviderDecorator(
            $provider1->reveal(),
            $cache->reveal()
        );

        $cache->get(Argument::type('string'))
            ->shouldBeCalled()
            ->willReturn(null);

        $cache->set(Argument::type('string'), json_encode($metadata), null)
            ->shouldBeCalled();

        self::assertSame($metadata, $provider->fetch($uri));
    }

    public function testShouldReuseCachedData(): void
    {
        $uri = 'https://example.com';
        $provider1 = $this->prophesize(RemoteProviderInterface::class);
        $cache = $this->prophesize(CacheInterface::class);

        $metadata = [
            'foo1' => 'bar1',
        ];
        $provider1->fetch($uri)->shouldNotBeCalled();

        $provider = new CachedProviderDecorator(
            $provider1->reveal(),
            $cache->reveal()
        );

        $cache->get(Argument::type('string'))
            ->shouldBeCalled()
            ->willReturn(json_encode($metadata));

        $cache->set(Argument::cetera())
            ->shouldNotBeCalled();

        self::assertSame($metadata, $provider->fetch($uri));
    }
}
