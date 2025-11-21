<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Cliente;

class ClienteController
{
    private $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    public function index(Request $request, Response $response): Response
    {
        $clientes = $this->clienteModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $clientes
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $cliente = $this->clienteModel->findById($id);
        
        if (!$cliente) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $cliente
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

        $id = $this->clienteModel->create($data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Cliente creado exitosamente',
            'id' => $id
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}

