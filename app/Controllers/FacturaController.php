<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Factura;
use OrionERP\Services\FacturacionService;

class FacturaController
{
    private $facturaModel;
    private $facturacionService;

    public function __construct()
    {
        $this->facturaModel = new Factura();
        $this->facturacionService = new FacturacionService();
    }

    public function index(Request $request, Response $response): Response
    {
        $facturas = $this->facturaModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $facturas
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function generarDesdePedido(Request $request, Response $response, array $args): Response
    {
        $pedidoId = (int) $args['pedido_id'];
        
        try {
            $facturaId = $this->facturacionService->generarFacturaDesdePedido($pedidoId);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Factura generada exitosamente',
                'factura_id' => $facturaId
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function marcarPagada(Request $request, Response $response, array $args): Response
    {
        $facturaId = (int) $args['id'];
        
        $this->facturacionService->marcarFacturaPagada($facturaId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Factura marcada como pagada'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

