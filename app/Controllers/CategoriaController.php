<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Categoria;

class CategoriaController
{
    private $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new Categoria();
    }

    public function index(Request $request, Response $response): Response
    {
        $categorias = $this->categoriaModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $categorias
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getArbol(Request $request, Response $response): Response
    {
        $arbol = $this->categoriaModel->getArbol();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $arbol
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $categoria = $this->categoriaModel->findById($id);
        
        if (!$categoria) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $categoria
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

        $id = $this->categoriaModel->create($data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Categoría creada exitosamente',
            'id' => $id
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $data = $request->getParsedBody();
        
        $categoria = $this->categoriaModel->findById($id);
        if (!$categoria) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $this->categoriaModel->update($id, $data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

