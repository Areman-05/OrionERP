<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\ReporteVentasService;

class ReporteVentasController
{
    private $reporteService;

    public function __construct()
    {
        $this->reporteService = new ReporteVentasService();
    }

    public function reporteMensual(Request $request, Response $response, array $args): Response
    {
        $mes = (int) ($args['mes'] ?? date('m'));
        $ano = (int) ($args['ano'] ?? date('Y'));

        $reporte = $this->reporteService->generarReporteMensual($mes, $ano);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $reporte
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function reporteAnual(Request $request, Response $response, array $args): Response
    {
        $ano = (int) ($args['ano'] ?? date('Y'));

        $reporte = $this->reporteService->generarReporteAnual($ano);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $reporte
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

