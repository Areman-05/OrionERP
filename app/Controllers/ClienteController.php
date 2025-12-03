<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Cliente;
use OrionERP\Services\ClienteService;

class ClienteController
{
    private $clienteModel;
    private $clienteService;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
        $this->clienteService = new ClienteService();
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

        $estadisticas = $this->clienteService->getEstadisticasCliente($id);
        $cliente['estadisticas'] = $estadisticas;
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $cliente
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getMorosos(Request $request, Response $response): Response
    {
        $clientes = $this->clienteService->getClientesMorosos();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $clientes
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}
