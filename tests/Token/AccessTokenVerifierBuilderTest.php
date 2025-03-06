<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Token;

use Facile\JoseVerifier\TokenVerifierInterface;
use Facile\OpenIDClient\Token\AccessTokenVerifierBuilder;
use Facile\OpenIDClientTest\TestCase;

class AccessTokenVerifierBuilderTest extends TestCase
{
    use ClientInterfaceTrait;

    public function testBuildInstance(): void
    {
        $builder = new AccessTokenVerifierBuilder();

        $instance = $builder->build($this->getClientInterface());

        $this->assertInstanceOf(TokenVerifierInterface::class, $instance);
    }
}