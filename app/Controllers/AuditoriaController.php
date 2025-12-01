<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\AuditoriaService;

class AuditoriaController
{
    private $auditoriaService;

    public function __construct()
    {
        $this->auditoriaService = new AuditoriaService();
    }

    public function getPorUsuario(Request $request, Response $response, array $args): Response
    {
        $usuarioId = (int) $args['usuario_id'];
        $queryParams = $request->getQueryParams();
        $limit = (int) ($queryParams['limit'] ?? 100);
        
        $logs = $this->auditoriaService->getLogsPorUsuario($usuarioId, $limit);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $logs
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPorTabla(Request $request, Response $response, array $args): Response
    {
        $tabla = $args['tabla'];
        $queryParams = $request->getQueryParams();
        $limit = (int) ($queryParams['limit'] ?? 100);
        
        $logs = $this->auditoriaService->getLogsPorTabla($tabla, $limit);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $logs
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getResumen(Request $request, Response $response): Response
    {
        $resumen = $this->auditoriaService->getResumenAuditoria();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $resumen
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

