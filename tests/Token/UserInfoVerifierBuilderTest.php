<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Token;

use Facile\JoseVerifier\TokenVerifierInterface;
use Facile\OpenIDClient\Token\UserInfoVerifierBuilder;
use Facile\OpenIDClientTest\TestCase;

class UserInfoVerifierBuilderTest extends TestCase
{
    use ClientInterfaceTrait;

    public function testBuildInstance(): void
    {
        $builder = new UserInfoVerifierBuilder();

        $instance = $builder->build($this->getClientInterface());

        $this->assertInstanceOf(TokenVerifierInterface::class, $instance);
    }
}
