<?php

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'aliases' => [
            'httplug.uri_factory' => \Zend\Diactoros\UriFactory::class,
            'httplug.stream_factory' => \Zend\Diactoros\StreamFactory::class,
        ],
        'factories' => [
            \Http\Message\StreamFactory\DiactorosStreamFactory::class => InvokableFactory::class,
            \Http\Message\UriFactory\DiactorosUriFactory::class => InvokableFactory::class,
        ],
    ],
    'httplug' => [
        'clients' => [
            'default' => [
                'factory' => 'httplug.client_factory.curl',
                'config' => [
                    CURLOPT_AUTOREFERER => true,
                    CURLOPT_CONNECTTIMEOUT_MS => 1000,
                    CURLOPT_TIMEOUT_MS => 120000,
                ],
                'plugins' => [
                    'header_defaults' => [
                        'name' => 'header_defaults',
                        'config' => [
                            'headers' => [
                                'user-agent' => 'tmv_php-openid-client',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];