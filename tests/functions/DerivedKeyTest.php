<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\functions;

use Facile\OpenIDClientTest\TestCase;
use function Facile\OpenIDClient\derived_key;

/**
 * @internal
 * @coversNothing
 */
final class DerivedKeyTest extends TestCase
{
    /**
     * @dataProvider valuesProvider
     */
    public function testDerivedKey(string $secret, int $length, string $expected): void
    {
        $jwk = derived_key($secret, $length);
        self::assertSame('oct', $jwk->get('kty'));
        self::assertSame($expected, $jwk->get('k'));
    }

    public function valuesProvider(): array
    {
        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return [
            [$string, 128, 'zwBxoIOtPkc0nS4_vIltBw'],
            [$string, 192, 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-'],
            [$string, 256, 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-OX1Akb-OcTs'],
            [$string, 384, 'zwBxoIOtPkc0nS4_vIltB6DVBYCzNcN-OX1Akb-OcTs'],
        ];
    }
}
