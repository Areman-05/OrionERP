<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\InventarioService;

class InventarioController
{
    private $inventarioService;

    public function __construct()
    {
        $this->inventarioService = new InventarioService();
    }

    public function getResumen(Request $request, Response $response): Response
    {
        $resumen = $this->inventarioService->getResumenInventario();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $resumen
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPorCategoria(Request $request, Response $response): Response
    {
        $datos = $this->inventarioService->getInventarioPorCategoria();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $datos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getSinStock(Request $request, Response $response): Response
    {
        $productos = $this->inventarioService->getProductosSinStock();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $productos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

