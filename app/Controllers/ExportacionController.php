<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\ExportacionService;

class ExportacionController
{
    private $exportacionService;

    public function __construct()
    {
        $this->exportacionService = new ExportacionService();
    }

    public function exportarProductos(Request $request, Response $response): Response
    {
        $csv = $this->exportacionService->exportarProductos();
        
        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="productos_' . date('Y-m-d') . '.csv"');
    }

    public function exportarClientes(Request $request, Response $response): Response
    {
        $csv = $this->exportacionService->exportarClientes();
        
        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="clientes_' . date('Y-m-d') . '.csv"');
    }

    public function exportarVentas(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $fechaInicio = $queryParams['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $queryParams['fecha_fin'] ?? date('Y-m-d');
        
        $csv = $this->exportacionService->exportarVentas($fechaInicio, $fechaFin);
        
        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="ventas_' . date('Y-m-d') . '.csv"');
    }
}

