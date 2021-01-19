<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Exception;

use Facile\OpenIDClient\Exception\ExceptionInterface;
use Facile\OpenIDClient\Exception\RuntimeException;
use Facile\OpenIDClientTest\TestCase;

class RuntimeExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new RuntimeException();
        static::assertInstanceOf(ExceptionInterface::class, $exception);
    }
}
