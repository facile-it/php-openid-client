<?php

chdir(__DIR__);

require_once __DIR__ . '/../../vendor/autoload.php';

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Facile\OpenIDClient\ConformanceTest\Command\RpTest;

/** @var ContainerInterface $container */
$container = require __DIR__ .'/../config/container.php';

$application = new Application();

$application->add($container->get(RpTest::class));

try {
    exit($application->run());
} catch (Throwable $e) {
    echo $e;
    exit(1);
}
