<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\ApiDocumentationService;

class ApiDocumentationController
{
    private $documentationService;

    public function __construct()
    {
        $this->documentationService = new ApiDocumentationService();
    }

    public function index(Request $request, Response $response): Response
    {
        $documentacion = $this->documentationService->generarDocumentacion();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $documentacion
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function endpoint(Request $request, Response $response, array $args): Response
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        
        $info = $this->documentationService->getEndpointInfo($method, $path);
        
        if ($info) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $info
            ]));
        } else {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Endpoint no documentado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

