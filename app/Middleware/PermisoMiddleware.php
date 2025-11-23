<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use OrionERP\Services\PermisoService;

class PermisoMiddleware implements MiddlewareInterface
{
    private $permisoService;
    private $modulo;
    private $permiso;

    public function __construct(string $modulo, string $permiso)
    {
        $this->permisoService = new PermisoService();
        $this->modulo = $modulo;
        $this->permiso = $permiso;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $rol = $request->getAttribute('rol');
        
        if (!$rol) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Rol no identificado'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        }
        
        if (!$this->permisoService->tienePermiso($rol, $this->modulo, $this->permiso)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'No tiene permiso para realizar esta acción'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        }
        
        return $handler->handle($request);
    }
}

