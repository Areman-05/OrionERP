<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Usuario;

class UsuarioController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function index(Request $request, Response $response): Response
    {
        $usuarios = $this->usuarioModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $usuarios
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $usuario = $this->usuarioModel->findById($id);
        
        if (!$usuario) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $usuario
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

