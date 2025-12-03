<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        
        // Asegurar que todas las respuestas JSON tengan el header correcto
        if ($response->getHeaderLine('Content-Type') === 'application/json' || 
            strpos($request->getUri()->getPath(), '/api/') === 0) {
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
        
        return $response;
    }
}

