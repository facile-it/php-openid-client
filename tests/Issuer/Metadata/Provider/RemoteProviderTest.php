<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Issuer\Metadata\Provider;

use Facile\OpenIDClient\Exception\RuntimeException;
use Facile\OpenIDClient\Issuer\Metadata\Provider\RemoteProvider;
use Facile\OpenIDClient\Issuer\Metadata\Provider\RemoteProviderInterface;
use PHPUnit\Framework\TestCase;

class RemoteProviderTest extends TestCase
{
    public function testShouldReturnDataFromFirstProvider(): void
    {
        $uri = 'https://example.com';
        $provider1 = $this->prophesize(RemoteProviderInterface::class);
        $provider2 = $this->prophesize(RemoteProviderInterface::class);

        $provider1->isAllowedUri($uri)->willReturn(true);
        $provider2->isAllowedUri($uri)->willReturn(true);

        $provider1->fetch($uri)->shouldBeCalled()->willReturn([
            'foo1' => 'bar1',
        ]);
        $provider2->fetch($uri)->shouldNotBeCalled();

        $provider = new RemoteProvider([
            $provider1->reveal(),
            $provider2->reveal(),
        ]);

        $this->assertSame(['foo1' => 'bar1'], $provider->fetch($uri));
    }

    public function testShouldFallbackOnNextProvider(): void
    {
        $uri = 'https://example.com';
        $provider1 = $this->prophesize(RemoteProviderInterface::class);
        $provider2 = $this->prophesize(RemoteProviderInterface::class);

        $provider1->isAllowedUri($uri)->willReturn(true);
        $provider2->isAllowedUri($uri)->willReturn(true);

        $provider1->fetch($uri)->shouldBeCalled()->willThrow(new RuntimeException('Error'));
        $provider2->fetch($uri)->shouldBeCalled()->willReturn([
            'foo1' => 'bar1',
        ]);

        $provider = new RemoteProvider([
            $provider1->reveal(),
            $provider2->reveal(),
        ]);

        $this->assertSame(['foo1' => 'bar1'], $provider->fetch($uri));
    }

    public function testShouldSkipIncompatibleProvider(): void
    {
        $uri = 'https://example.com';
        $provider1 = $this->prophesize(RemoteProviderInterface::class);
        $provider2 = $this->prophesize(RemoteProviderInterface::class);

        $provider1->isAllowedUri($uri)->willReturn(false);
        $provider2->isAllowedUri($uri)->willReturn(true);

        $provider1->fetch($uri)->shouldNotBeCalled();
        $provider2->fetch($uri)->shouldBeCalled()->willReturn([
            'foo1' => 'bar1',
        ]);

        $provider = new RemoteProvider([
            $provider1->reveal(),
            $provider2->reveal(),
        ]);

        $this->assertSame(['foo1' => 'bar1'], $provider->fetch($uri));
    }
}
