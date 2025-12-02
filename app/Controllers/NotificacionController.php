<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\NotificacionService;

class NotificacionController
{
    private $notificacionService;

    public function __construct()
    {
        $this->notificacionService = new NotificacionService();
    }

    public function getNoLeidas(Request $request, Response $response): Response
    {
        $usuarioId = $request->getAttribute('usuario_id', 0);
        
        if ($usuarioId === 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Usuario no identificado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        
        $notificaciones = $this->notificacionService->getNoLeidas($usuarioId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $notificaciones,
            'total' => count($notificaciones)
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function marcarLeida(Request $request, Response $response, array $args): Response
    {
        $notificacionId = (int) $args['id'];
        $usuarioId = $request->getAttribute('usuario_id', 0);
        
        $this->notificacionService->marcarLeida($notificacionId, $usuarioId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}


