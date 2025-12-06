<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Factura;
use OrionERP\Services\FacturacionService;
use OrionERP\Services\FacturaService;

class FacturaController
{
    private $facturaModel;
    private $facturacionService;
    private $facturaService;

    public function __construct()
    {
        $this->facturaModel = new Factura();
        $this->facturacionService = new FacturacionService();
        $this->facturaService = new FacturaService();
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

    public function getPendientes(Request $request, Response $response): Response
    {
        $facturas = $this->facturaService->getFacturasPendientes();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $facturas
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getVencidas(Request $request, Response $response): Response
    {
        $facturas = $this->facturaService->getFacturasVencidas();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $facturas
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getEstadisticas(Request $request, Response $response, array $args): Response
    {
        $mes = (int) ($args['mes'] ?? date('m'));
        $ano = (int) ($args['ano'] ?? date('Y'));

        $estadisticas = $this->facturaService->getEstadisticasFacturacion($mes, $ano);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $estadisticas
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPorCliente(Request $request, Response $response, array $args): Response
    {
        $clienteId = (int) $args['cliente_id'];
        $facturas = $this->facturaService->getFacturasPorCliente($clienteId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $facturas
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

