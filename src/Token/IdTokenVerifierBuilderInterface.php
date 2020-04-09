<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Token;

use Facile\OpenIDClient\Client\ClientInterface;

interface IdTokenVerifierBuilderInterface
{
    public function build(ClientInterface $client): \Facile\JoseVerifier\IdTokenVerifierInterface;
}
