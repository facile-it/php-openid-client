<?php

declare(strict_types=1);

namespace Facile\OpenIDClient;

use Psr\Http\Message\ResponseInterface;
use Facile\OpenIDClient\Exception\OAuth2Exception;

/**
 * @param ResponseInterface $response
 * @param int|null $expectedCode
 */
function check_server_response(ResponseInterface $response, ?int $expectedCode = null): void
{
    if (null === $expectedCode && $response->getStatusCode() >= 400) {
        throw OAuth2Exception::fromResponse($response);
    }

    if (null !== $expectedCode && $expectedCode !== $response->getStatusCode()) {
        throw OAuth2Exception::fromResponse($response);
    }
}
