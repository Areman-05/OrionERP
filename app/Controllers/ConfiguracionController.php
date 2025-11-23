<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\ConfiguracionEmpresa;

class ConfiguracionController
{
    private $configModel;

    public function __construct()
    {
        $this->configModel = new ConfiguracionEmpresa();
    }

    public function getAll(Request $request, Response $response): Response
    {
        $configs = $this->configModel->getAll();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $configs
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $clave = $args['clave'];
        $valor = $this->configModel->get($clave);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => ['clave' => $clave, 'valor' => $valor]
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['clave']) || !isset($data['valor'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Clave y valor son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $tipo = $data['tipo'] ?? 'texto';
        $this->configModel->set($data['clave'], $data['valor'], $tipo);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Configuración actualizada exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

