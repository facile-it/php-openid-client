<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Token;

use Facile\JoseVerifier\TokenVerifierInterface;
use Facile\OpenIDClient\Token\ResponseVerifierBuilder;
use Facile\OpenIDClientTest\TestCase;

class ResponseVerifierBuilderTest extends TestCase
{
    use ClientInterfaceTrait;

    public function testBuildInstance(): void
    {
        $builder = new ResponseVerifierBuilder();

        $instance = $builder->build($this->getClientInterface());

        $this->assertInstanceOf(TokenVerifierInterface::class, $instance);
    }
}