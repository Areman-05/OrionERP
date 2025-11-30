<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\ApiService;
use OrionERP\Models\Usuario;

class ApiController
{
    private $apiService;
    private $usuarioModel;

    public function __construct()
    {
        $this->apiService = new ApiService();
        $this->usuarioModel = new Usuario();
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['email']) || empty($data['password'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Email y contraseña son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $usuario = $this->usuarioModel->findByEmail($data['email']);
        
        if (!$usuario || !password_verify($data['password'], $usuario['password'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        if (!$usuario['activo']) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Usuario inactivo'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $token = $this->apiService->generarToken($usuario['id'], $usuario['rol']);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'token' => $token,
            'usuario' => [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email'],
                'rol' => $usuario['rol']
            ]
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function verificarToken(Request $request, Response $response): Response
    {
        $usuarioId = $request->getAttribute('usuario_id');
        $rol = $request->getAttribute('rol');
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'usuario_id' => $usuarioId,
            'rol' => $rol
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

