<?php

declare(strict_types=1);

use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

/** @var array $config */
$config = require __DIR__ . '/config.php';

$container = new ServiceManager($config['dependencies'] ?? []);
$container->setService('config', $config);
$container->setService(ContainerInterface::class, $container);

return $container;
