<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Token;

interface TokenSetFactoryInterface
{
    /**
     * @param array<string, mixed> $array
     *
     * @return TokenSetInterface
     */
    public function fromArray(array $array): TokenSetInterface;
}
