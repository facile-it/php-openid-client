<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\OpenIDClient\AuthMethod\AuthMethodFactory;
use Facile\OpenIDClient\AuthMethod\AuthMethodInterface;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use Facile\OpenIDClientTest\TestCase;

class AuthMethodFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $authMethod1 = $this->prophesize(AuthMethodInterface::class);
        $authMethod2 = $this->prophesize(AuthMethodInterface::class);

        $authMethod1->getSupportedMethod()->willReturn('foo');
        $authMethod2->getSupportedMethod()->willReturn('bar');

        $factory = new AuthMethodFactory([
            $authMethod1->reveal(),
            $authMethod2->reveal(),
        ]);

        static::assertCount(2, $factory->all());
        static::assertSame($authMethod1->reveal(), $factory->create('foo'));
        static::assertSame($authMethod2->reveal(), $factory->create('bar'));
    }

    public function testCreateWithUnknownAuthMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = new AuthMethodFactory();

        $factory->create('foo');
    }
}
