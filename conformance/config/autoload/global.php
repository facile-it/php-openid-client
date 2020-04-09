<?php

use Laminas\Di\Container;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'abstract_factories' => [
            Container\ServiceManager\AutowireFactory::class
        ],
        'aliases' => [
            \Psr\Http\Message\ServerRequestInterface::class => \Laminas\Diactoros\ServerRequestFactory::class,
            \Psr\Http\Message\RequestFactoryInterface::class => \Laminas\Diactoros\RequestFactory::class,
            \Psr\Http\Message\ResponseFactoryInterface::class => \Laminas\Diactoros\ResponseFactory::class,
            \Psr\Http\Message\UriFactoryInterface::class => \Laminas\Diactoros\UriFactory::class,
            \Psr\Http\Message\StreamFactoryInterface::class => \Laminas\Diactoros\StreamFactory::class,
        ],
        'factories' => [
            \Laminas\Diactoros\ServerRequestFactory::class => InvokableFactory::class,
            \Laminas\Diactoros\RequestFactory::class => InvokableFactory::class,
            \Laminas\Diactoros\ResponseFactory::class => InvokableFactory::class,
            \Laminas\Diactoros\UriFactory::class => InvokableFactory::class,
            \Laminas\Diactoros\StreamFactory::class => InvokableFactory::class,
        ],
        'auto' => [
            'types' => [

            ],
        ],
    ],
];