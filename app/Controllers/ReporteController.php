<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\ReportePdfService;

class ReporteController
{
    private $reportePdfService;

    public function __construct()
    {
        $this->reportePdfService = new ReportePdfService();
    }

    public function generarReporteVentas(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $fechaInicio = $queryParams['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $queryParams['fecha_fin'] ?? date('Y-m-d');
        
        try {
            $archivo = $this->reportePdfService->generarReporteVentas($fechaInicio, $fechaFin);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Reporte generado exitosamente',
                'archivo' => basename($archivo)
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

    public function generarReporteStock(Request $request, Response $response): Response
    {
        try {
            $archivo = $this->reportePdfService->generarReporteStock();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Reporte generado exitosamente',
                'archivo' => basename($archivo)
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

