<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\EstadisticasService;

class EstadisticasController
{
    private $estadisticasService;

    public function __construct()
    {
        $this->estadisticasService = new EstadisticasService();
    }

    public function getKPIs(Request $request, Response $response): Response
    {
        $kpis = $this->estadisticasService->getKPIs();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $kpis
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getVentasPorMes(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $year = (int) ($queryParams['year'] ?? date('Y'));
        
        $datos = $this->estadisticasService->getVentasPorMes($year);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $datos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getProductosMasVendidos(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $limit = (int) ($queryParams['limit'] ?? 10);
        
        $datos = $this->estadisticasService->getProductosMasVendidos($limit);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $datos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getBeneficios(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $year = (int) ($queryParams['year'] ?? date('Y'));
        
        $datos = $this->estadisticasService->getBeneficios($year);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $datos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

