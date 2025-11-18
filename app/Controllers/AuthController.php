<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Usuario;

class AuthController
{
    private $usuarioModel;

    public function __construct()
    {
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
        
        if (!$usuario || !$this->usuarioModel->verifyPassword($data['password'], $usuario['password'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Actualizar último acceso
        $this->usuarioModel->updateLastAccess($usuario['id']);

        // En una implementación completa, aquí se generaría un JWT
        unset($usuario['password']);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Login exitoso',
            'usuario' => $usuario
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Logout exitoso'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

