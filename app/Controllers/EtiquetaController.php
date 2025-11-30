<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Etiqueta;

class EtiquetaController
{
    private $etiquetaModel;

    public function __construct()
    {
        $this->etiquetaModel = new Etiqueta();
    }

    public function index(Request $request, Response $response): Response
    {
        $etiquetas = $this->etiquetaModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $etiquetas
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['nombre'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'El nombre es requerido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $id = $this->etiquetaModel->create($data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Etiqueta creada exitosamente',
            'id' => $id
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function agregarAProducto(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['producto_id'];
        $etiquetaId = (int) $args['etiqueta_id'];
        
        $this->etiquetaModel->agregarAProducto($productoId, $etiquetaId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Etiqueta agregada al producto'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

