<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\functions;

use function Facile\OpenIDClient\base64url_encode;
use PHPUnit\Framework\TestCase;

class Base64UrlEncodeTest extends TestCase
{
    public function testBase64UrlEncode(): void
    {
        static::assertSame('Zm9v', base64url_encode('foo'));
        static::assertSame('YUJjRGVGZ0hpSmtMbU5vUHFSc1R1VndYeVowMTIzNDU2Nzg5', base64url_encode('aBcDeFgHiJkLmNoPqRsTuVwXyZ0123456789'));
    }
}
