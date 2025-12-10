<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ApiResponseMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        // Agregar headers estándar de API
        $response = $response->withHeader('X-API-Version', '1.0');
        $response = $response->withHeader('X-Request-ID', $this->generateRequestId());

        // Si la respuesta no tiene Content-Type, agregarlo
        if (!$response->hasHeader('Content-Type')) {
            $response = $response->withHeader('Content-Type', 'application/json');
        }

        return $response;
    }

    private function generateRequestId(): string
    {
        return bin2hex(random_bytes(16));
    }
}

