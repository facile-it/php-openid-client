<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Exception;

use Facile\OpenIDClient\Exception\ExceptionInterface;
use Facile\OpenIDClient\Exception\LogicException;
use Facile\OpenIDClientTest\TestCase;

class LogicExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new LogicException();
        static::assertInstanceOf(ExceptionInterface::class, $exception);
    }
}
