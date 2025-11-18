<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        // En una implementación completa, aquí se validaría el JWT
        // Por ahora, solo verificamos si existe un header de autorización
        
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'No autorizado. Token requerido.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Si hay token, continuar con la petición
        return $handler->handle($request);
    }
}

