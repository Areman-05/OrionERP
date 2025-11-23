<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\PedidoCompra;
use OrionERP\Models\Proveedor;

class CompraController
{
    private $pedidoCompraModel;
    private $proveedorModel;

    public function __construct()
    {
        $this->pedidoCompraModel = new PedidoCompra();
        $this->proveedorModel = new Proveedor();
    }

    public function index(Request $request, Response $response): Response
    {
        $pedidos = $this->pedidoCompraModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $pedidos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $pedido = $this->pedidoCompraModel->findById($id);
        
        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Pedido de compra no encontrado'
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
        
        if (empty($data['proveedor_id']) || empty($data['usuario_id'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Proveedor y usuario son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $pedidoId = $this->pedidoCompraModel->create($data);
        
        // Agregar líneas si existen
        if (!empty($data['lineas']) && is_array($data['lineas'])) {
            foreach ($data['lineas'] as $linea) {
                $this->pedidoCompraModel->agregarLinea($pedidoId, $linea);
            }
            $this->pedidoCompraModel->actualizarTotales($pedidoId);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Pedido de compra creado exitosamente',
            'id' => $pedidoId
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $data = $request->getParsedBody();
        
        $pedido = $this->pedidoCompraModel->findById($id);
        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Pedido de compra no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->pedidoCompraModel->update($id, $data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Pedido de compra actualizado exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

