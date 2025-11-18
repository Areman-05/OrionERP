<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RoleMiddleware implements MiddlewareInterface
{
    private $allowedRoles;

    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        // En una implementación completa, aquí se obtendría el rol del usuario desde el JWT
        // Por ahora, solo validamos la estructura
        
        $userRole = $request->getAttribute('user_role', null);
        
        if ($userRole === null || !in_array($userRole, $this->allowedRoles)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'No tienes permisos para acceder a este recurso'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        return $handler->handle($request);
    }
}

