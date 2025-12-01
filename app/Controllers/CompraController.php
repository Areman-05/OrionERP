<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\PedidoCompra;
use OrionERP\Services\CompraService;

class CompraController
{
    private $pedidoCompraModel;
    private $compraService;

    public function __construct()
    {
        $this->pedidoCompraModel = new PedidoCompra();
        $this->compraService = new CompraService();
    }

    public function getResumen(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $fechaInicio = $queryParams['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $queryParams['fecha_fin'] ?? date('Y-m-d');
        
        $resumen = $this->compraService->getResumenCompras($fechaInicio, $fechaFin);
        $porProveedor = $this->compraService->getComprasPorProveedor($fechaInicio, $fechaFin);
        $atrasados = $this->compraService->getPedidosAtrasados();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'resumen' => $resumen,
                'por_proveedor' => $porProveedor,
                'pedidos_atrasados' => $atrasados
            ]
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}
