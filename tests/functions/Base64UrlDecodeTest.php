<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\functions;

use function Facile\OpenIDClient\base64url_decode;
use Facile\OpenIDClientTest\TestCase;

class Base64UrlDecodeTest extends TestCase
{
    public function testBase64UrlEncode(): void
    {
        static::assertSame('foo', base64url_decode('Zm9v'));
        static::assertSame('aBcDeFgHiJkLmNoPqRsTuVwXyZ0123456789', base64url_decode('YUJjRGVGZ0hpSmtMbU5vUHFSc1R1VndYeVowMTIzNDU2Nzg5'));
    }
}
