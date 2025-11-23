<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\InformeService;

class InformeController
{
    private $informeService;

    public function __construct()
    {
        $this->informeService = new InformeService();
    }

    public function informeVentas(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $mes = $queryParams['mes'] ?? date('m');
        $ano = $queryParams['ano'] ?? date('Y');
        
        $datos = $this->informeService->informeVentasMensual($mes, $ano);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $datos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function informeGastos(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $fechaInicio = $queryParams['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $queryParams['fecha_fin'] ?? date('Y-m-d');
        
        $datos = $this->informeService->informeGastos($fechaInicio, $fechaFin);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $datos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function informeStock(Request $request, Response $response): Response
    {
        $datos = $this->informeService->informeStock();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $datos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

