<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\RotacionService;

class RotacionController
{
    private $rotacionService;

    public function __construct()
    {
        $this->rotacionService = new RotacionService();
    }

    public function getRotacion(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['producto_id'];
        $queryParams = $request->getQueryParams();
        $periodoDias = (int) ($queryParams['periodo'] ?? 30);
        
        $rotacion = $this->rotacionService->calcularRotacion($productoId, $periodoDias);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $rotacion
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getRotacionBaja(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $limit = (int) ($queryParams['limit'] ?? 20);
        
        $productos = $this->rotacionService->getProductosRotacionBaja($limit);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $productos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

