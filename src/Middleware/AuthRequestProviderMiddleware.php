<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Middleware;

use Facile\OpenIDClient\Authorization\AuthRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Override;

/**
 * @psalm-api
 */
final readonly class AuthRequestProviderMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthRequestInterface $authRequest
    ) {}

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute(AuthRequestInterface::class, $this->authRequest));
    }
}
