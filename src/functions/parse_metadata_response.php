<?php

declare(strict_types=1);

namespace Facile\OpenIDClient;

use Facile\OpenIDClient\Exception\InvalidArgumentException;
use function is_array;
use function json_decode;
use Psr\Http\Message\ResponseInterface;

/**
 * @param ResponseInterface $response
 * @param int|null $expectedCode
 *
 * @return array<string, mixed>
 */
function parse_metadata_response(ResponseInterface $response, ?int $expectedCode = null): array
{
    check_server_response($response, $expectedCode);

    /** @var bool|array<string, mixed> $data */
    $data = json_decode((string) $response->getBody(), true);

    if (! is_array($data)) {
        throw new InvalidArgumentException('Invalid metadata content');
    }

    return $data;
}
