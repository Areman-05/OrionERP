<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Producto;

class ProductoController
{
    private $productoModel;

    public function __construct()
    {
        $this->productoModel = new Producto();
    }

    public function index(Request $request, Response $response): Response
    {
        $productos = $this->productoModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $productos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $producto = $this->productoModel->findById($id);
        
        if (!$producto) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $producto
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['nombre']) || empty($data['codigo'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Nombre y codigo son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $id = $this->productoModel->create($data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'id' => $id
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $data = $request->getParsedBody();
        
        $producto = $this->productoModel->findById($id);
        if (!$producto) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->productoModel->update($id, $data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Producto actualizado exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

