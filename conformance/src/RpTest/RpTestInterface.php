<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest;

use Facile\OpenIDClient\ConformanceTest\TestInfo;

interface RpTestInterface
{
    public function execute(TestInfo $testInfo): void;

    public function getTestId(): string;
}
