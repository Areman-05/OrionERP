<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\HistoricoProducto;

class HistoricoController
{
    private $historicoModel;

    public function __construct()
    {
        $this->historicoModel = new HistoricoProducto();
    }

    public function getByProducto(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['producto_id'];
        $historico = $this->historicoModel->getByProducto($productoId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $historico
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAll(Request $request, Response $response): Response
    {
        $historico = $this->historicoModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $historico
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

