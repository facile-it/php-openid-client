<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Token;

use Facile\JoseVerifier\TokenVerifierInterface;
use Facile\OpenIDClient\Token\IdTokenVerifierBuilder;
use Facile\OpenIDClientTest\TestCase;

class IdTokenVerifierBuilderTest extends TestCase
{
    use ClientInterfaceTrait;

    public function testBuildInstance(): void
    {
        $builder = new IdTokenVerifierBuilder();

        $instance = $builder->build($this->getClientInterface());

        $this->assertInstanceOf(TokenVerifierInterface::class, $instance);
    }
}