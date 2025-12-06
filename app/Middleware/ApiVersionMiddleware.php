<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApiVersionMiddleware implements MiddlewareInterface
{
    private $defaultVersion;

    public function __construct(string $defaultVersion = 'v1')
    {
        $this->defaultVersion = $defaultVersion;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        
        // Extraer versión de la URL o header
        if (preg_match('#/api/v(\d+)/#', $path, $matches)) {
            $version = 'v' . $matches[1];
        } else {
            $version = $request->getHeaderLine('X-API-Version') ?: $this->defaultVersion;
        }

        $request = $request->withAttribute('api_version', $version);
        
        return $handler->handle($request);
    }
}

