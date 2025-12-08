<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\ReporteService;

class ReporteController
{
    private $reporteService;

    public function __construct()
    {
        $this->reporteService = new ReporteService();
    }

    public function reporteVentas(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $fechaInicio = $queryParams['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $queryParams['fecha_fin'] ?? date('Y-m-d');

        $reporte = $this->reporteService->generarReporteVentas($fechaInicio, $fechaFin);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $reporte
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function reporteInventario(Request $request, Response $response): Response
    {
        $reporte = $this->reporteService->generarReporteInventario();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $reporte
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function reporteClientes(Request $request, Response $response): Response
    {
        $reporte = $this->reporteService->generarReporteClientes();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $reporte
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}
