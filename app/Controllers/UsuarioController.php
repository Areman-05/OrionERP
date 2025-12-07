<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Usuario;
use OrionERP\Services\UsuarioService;

class UsuarioController
{
    private $usuarioModel;
    private $usuarioService;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->usuarioService = new UsuarioService();
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

        $actividad = $this->usuarioService->getActividadUsuario($id);
        $usuario['actividad'] = $actividad;
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $usuario
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['nombre']) || empty($data['email']) || empty($data['password'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Nombre, email y contraseña son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $id = $this->usuarioService->crearUsuario($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'id' => $id
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function cambiarPassword(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $usuarioId = $request->getAttribute('usuario_id', 0);
        
        if (empty($data['password_actual']) || empty($data['password_nuevo'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Contraseña actual y nueva son requeridas'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->usuarioService->cambiarPassword(
                $usuarioId,
                $data['password_actual'],
                $data['password_nuevo']
            );
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function getActividad(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $queryParams = $request->getQueryParams();
        $dias = (int) ($queryParams['dias'] ?? 30);

        $actividad = $this->usuarioService->getActividadUsuario($id, $dias);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $actividad
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}
