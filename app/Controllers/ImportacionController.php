<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\ImportacionService;

class ImportacionController
{
    private $importacionService;

    public function __construct()
    {
        $this->importacionService = new ImportacionService();
    }

    public function importarProductos(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['csv_content'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Contenido CSV requerido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $resultado = $this->importacionService->importarProductos($data['csv_content']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => "Importados {$resultado['importados']} productos",
                'data' => $resultado
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

    public function importarClientes(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['csv_content'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Contenido CSV requerido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $resultado = $this->importacionService->importarClientes($data['csv_content']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => "Importados {$resultado['importados']} clientes",
                'data' => $resultado
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
}

