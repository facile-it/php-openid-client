<?php

declare(strict_types=1);

namespace Facile\OpenIDClient;

use function in_array;
use function parse_str;
use Psr\Http\Message\ServerRequestInterface;
use function strtoupper;
use Facile\OpenIDClient\Exception\RuntimeException;

/**
 * @param ServerRequestInterface $serverRequest
 *
 * @return array<string, mixed>
 */
function parse_callback_params(ServerRequestInterface $serverRequest): array
{
    $method = strtoupper($serverRequest->getMethod());

    if (! in_array($method, ['GET', 'POST'], true)) {
        throw new RuntimeException('Invalid callback method');
    }

    if ('POST' === $method) {
        parse_str((string) $serverRequest->getBody(), $params);
    } elseif ('' !== $serverRequest->getUri()->getFragment()) {
        parse_str($serverRequest->getUri()->getFragment(), $params);
    } else {
        parse_str($serverRequest->getUri()->getQuery(), $params);
    }

    return $params;
}
