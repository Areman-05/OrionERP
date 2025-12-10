<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\PedidoVenta;
use OrionERP\Models\Producto;
use OrionERP\Services\PedidoService;

class PedidoVentaController
{
    private $pedidoModel;
    private $productoModel;
    private $pedidoService;

    public function __construct()
    {
        $this->pedidoModel = new PedidoVenta();
        $this->productoModel = new Producto();
        $this->pedidoService = new PedidoService();
    }

    public function index(Request $request, Response $response): Response
    {
        $pedidos = $this->pedidoModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $pedidos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $pedido = $this->pedidoModel->findById($id);
        
        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $pedido
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['cliente_id']) || empty($data['usuario_id'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Cliente y usuario son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $pedidoId = $this->pedidoModel->create($data);
        
        // Agregar líneas si existen
        if (!empty($data['lineas']) && is_array($data['lineas'])) {
            foreach ($data['lineas'] as $linea) {
                $this->pedidoModel->agregarLinea($pedidoId, $linea);
                
                // Descontar stock
                if (!empty($linea['producto_id']) && !empty($linea['cantidad'])) {
                    $this->productoModel->decrementarStock(
                        $linea['producto_id'],
                        $linea['cantidad'],
                        'salida',
                        "Pedido venta: {$pedidoId}",
                        $data['usuario_id']
                    );
                }
            }
            $this->pedidoModel->actualizarTotales($pedidoId);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Pedido creado exitosamente',
            'id' => $pedidoId
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function updateEstado(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $data = $request->getParsedBody();
        
        $pedido = $this->pedidoModel->findById($id);
        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->pedidoModel->update($id, ['estado' => $data['estado']]);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Estado del pedido actualizado'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPendientes(Request $request, Response $response): Response
    {
        $pedidos = $this->pedidoService->getPedidosPendientes();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $pedidos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPorCliente(Request $request, Response $response, array $args): Response
    {
        $clienteId = (int) $args['cliente_id'];
        $queryParams = $request->getQueryParams();
        $limit = (int) ($queryParams['limit'] ?? 10);

        $pedidos = $this->pedidoService->getPedidosPorCliente($clienteId, $limit);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $pedidos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getEstadisticas(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $fechaInicio = $queryParams['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $queryParams['fecha_fin'] ?? date('Y-m-d');

        $estadisticas = $this->pedidoService->calcularEstadisticasPedidos($fechaInicio, $fechaFin);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $estadisticas
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

