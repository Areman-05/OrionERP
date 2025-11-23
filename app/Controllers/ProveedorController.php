<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Proveedor;

class ProveedorController
{
    private $proveedorModel;

    public function __construct()
    {
        $this->proveedorModel = new Proveedor();
    }

    public function index(Request $request, Response $response): Response
    {
        $proveedores = $this->proveedorModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $proveedores
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $proveedor = $this->proveedorModel->findById($id);
        
        if (!$proveedor) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $proveedor
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

        $id = $this->proveedorModel->create($data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Proveedor creado exitosamente',
            'id' => $id
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $data = $request->getParsedBody();
        
        $proveedor = $this->proveedorModel->findById($id);
        if (!$proveedor) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->proveedorModel->update($id, $data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Proveedor actualizado exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        
        $proveedor = $this->proveedorModel->findById($id);
        if (!$proveedor) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->proveedorModel->delete($id);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Proveedor eliminado exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

