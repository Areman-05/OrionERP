<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\BackupService;

class BackupController
{
    private $backupService;

    public function __construct()
    {
        $this->backupService = new BackupService();
    }

    public function crear(Request $request, Response $response): Response
    {
        try {
            $archivo = $this->backupService->crearBackup();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Backup creado exitosamente',
                'archivo' => basename($archivo)
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function listar(Request $request, Response $response): Response
    {
        $backups = $this->backupService->listarBackups();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $backups
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function restaurar(Request $request, Response $response, array $args): Response
    {
        $archivo = $args['archivo'] ?? null;
        
        if (!$archivo) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Archivo de backup requerido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        try {
            $this->backupService->restaurarBackup($archivo);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Backup restaurado exitosamente'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}

