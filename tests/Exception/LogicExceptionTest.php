<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Exception;

use PHPUnit\Framework\TestCase;
use Facile\OpenIDClient\Exception\ExceptionInterface;
use Facile\OpenIDClient\Exception\LogicException;

class LogicExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new LogicException();
        static::assertInstanceOf(ExceptionInterface::class, $exception);
    }
}
