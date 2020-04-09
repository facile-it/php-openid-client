<?php

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

/** @var array $config */
$config = require __DIR__ . '/config.php';

$container = new ServiceManager($config['dependencies'] ?? []);
$container->setService('config', $config);
$container->setService(ContainerInterface::class, $container);

return $container;
