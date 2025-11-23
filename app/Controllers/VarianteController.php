<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\VarianteProducto;

class VarianteController
{
    private $varianteModel;

    public function __construct()
    {
        $this->varianteModel = new VarianteProducto();
    }

    public function getByProducto(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['producto_id'];
        $variantes = $this->varianteModel->getByProducto($productoId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $variantes
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['producto_id']) || empty($data['codigo'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Producto y codigo son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $id = $this->varianteModel->create($data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Variante creada exitosamente',
            'id' => $id
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}

