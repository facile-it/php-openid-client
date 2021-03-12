<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @internal
 * @coversNothing
 */
abstract class TestCase extends BaseTestCase
{
    use ProphecyTrait;
}
