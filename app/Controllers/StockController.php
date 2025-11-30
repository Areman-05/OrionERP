<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\StockService;

class StockController
{
    private $stockService;

    public function __construct()
    {
        $this->stockService = new StockService();
    }

    public function getStockBajo(Request $request, Response $response): Response
    {
        $productos = $this->stockService->getProductosStockBajo();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $productos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ajustarStock(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['producto_id']) || !isset($data['cantidad']) || empty($data['motivo'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto, cantidad y motivo son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $usuarioId = $data['usuario_id'] ?? 0;
        $this->stockService->ajustarStock(
            $data['producto_id'],
            $data['cantidad'],
            $data['motivo'],
            $usuarioId
        );
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Stock ajustado exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getMovimientos(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['producto_id'];
        $movimientos = $this->stockService->getMovimientosStock($productoId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $movimientos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

