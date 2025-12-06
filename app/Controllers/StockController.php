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

    public function ajustarStock(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['id'];
        $data = $request->getParsedBody();
        
        $resultado = $this->stockService->ajustarStock(
            $productoId,
            (int) $data['cantidad'],
            $data['motivo'] ?? 'Ajuste manual',
            (int) $data['usuario_id']
        );
        
        if ($resultado) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Stock ajustado correctamente'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error al ajustar stock'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    public function getMovimientos(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['id'];
        $movimientos = $this->stockService->getMovimientosStock($productoId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $movimientos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function transferirStock(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        $resultado = $this->stockService->transferirStock(
            (int) $data['producto_id'],
            (int) $data['cantidad'],
            $data['origen'],
            $data['destino'],
            (int) $data['usuario_id']
        );
        
        if ($resultado) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Stock transferido correctamente'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Error al transferir stock'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
}
