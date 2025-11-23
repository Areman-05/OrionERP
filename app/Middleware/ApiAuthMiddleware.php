<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use OrionERP\Services\ApiService;

class ApiAuthMiddleware implements MiddlewareInterface
{
    private $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token de autenticación requerido'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $token = $matches[1];
        $payload = $this->apiService->validarToken($token);

        if (!$payload) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $request = $request->withAttribute('usuario_id', $payload['usuario_id']);
        $request = $request->withAttribute('rol', $payload['rol']);

        return $handler->handle($request);
    }
}

