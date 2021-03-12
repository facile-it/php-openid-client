<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\functions;

use Facile\OpenIDClientTest\TestCase;
use function Facile\OpenIDClient\base64url_decode;

/**
 * @internal
 * @coversNothing
 */
final class Base64UrlDecodeTest extends TestCase
{
    public function testBase64UrlEncode(): void
    {
        self::assertSame('foo', base64url_decode('Zm9v'));
        self::assertSame('aBcDeFgHiJkLmNoPqRsTuVwXyZ0123456789', base64url_decode('YUJjRGVGZ0hpSmtMbU5vUHFSc1R1VndYeVowMTIzNDU2Nzg5'));
    }
}
