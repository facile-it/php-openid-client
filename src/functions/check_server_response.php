<?php

declare(strict_types=1);

namespace Facile\OpenIDClient;

use Facile\OpenIDClient\Exception\OAuth2Exception;
use Psr\Http\Message\ResponseInterface;

function check_server_response(ResponseInterface $response, ?int $expectedCode = null): void
{
    if (null === $expectedCode && $response->getStatusCode() >= 400) {
        throw OAuth2Exception::fromResponse($response);
    }

    if (null !== $expectedCode && $response->getStatusCode() !== $expectedCode) {
        throw OAuth2Exception::fromResponse($response);
    }
}
