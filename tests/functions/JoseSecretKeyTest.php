<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\functions;

use function Facile\OpenIDClient\jose_secret_key;
use Facile\OpenIDClientTest\TestCase;

class JoseSecretKeyTest extends TestCase
{
    /**
     * @dataProvider valuesProvider
     *
     * @param string $secret
     * @param string $alg
     * @param string $expected
     */
    public function testJoseSecretKey(string $secret, string $alg, string $expected): void
    {
        $jwk = jose_secret_key($secret, $alg);
        static::assertSame('oct', $jwk->get('kty'));
        static::assertSame($expected, $jwk->get('k'));
    }

    public function valuesProvider(): array
    {
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return [
            [$string, 'A128GCM', 'zwBxoIOtPkc0nS4_vIltBw'],
            [$string, 'A192GCM', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-'],
            [$string, 'A256GCM', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-OX1Akb-OcTs'],
            [$string, 'A128KW', 'zwBxoIOtPkc0nS4_vIltBw'],
            [$string, 'A192KW', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-'],
            [$string, 'A256KW', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-OX1Akb-OcTs'],
            [$string, 'A128GCMKW', 'zwBxoIOtPkc0nS4_vIltBw'],
            [$string, 'A192GCMKW', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-'],
            [$string, 'A256GCMKW', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-OX1Akb-OcTs'],
            [$string, 'A128CBC-HS256', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-OX1Akb-OcTs'],
            [$string, 'A192CBC-HS384', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-OX1Akb-OcTs'],
            [$string, 'A192CBC-HS384', 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-OX1Akb-OcTs'],
            [$string, 'RS256', 'YWJjZGVmZ2hpamtsbW5vcHFyc3R1dnd4eXowMTIzNDU2Nzg5QUJDREVGR0hJSktMTU5PUFFSU1RVVldYWVo'],
        ];
    }
}
