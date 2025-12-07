<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\ExportacionService;
use OrionERP\Services\ExportacionExcelService;

class ExportacionController
{
    private $exportacionService;
    private $excelService;

    public function __construct()
    {
        $this->exportacionService = new ExportacionService();
        $this->excelService = new ExportacionExcelService();
    }

    public function exportarProductosExcel(Request $request, Response $response): Response
    {
        $csv = $this->excelService->exportarProductosExcel();
        
        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="productos_' . date('Y-m-d') . '.csv"');
    }

    public function exportarVentasExcel(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $fechaInicio = $queryParams['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $queryParams['fecha_fin'] ?? date('Y-m-d');
        
        $csv = $this->excelService->exportarVentasExcel($fechaInicio, $fechaFin);
        
        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="ventas_' . date('Y-m-d') . '.csv"');
    }

    public function exportarProductos(Request $request, Response $response): Response
    {
        $csv = $this->exportacionService->exportarProductos();
        
        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="productos_' . date('Y-m-d') . '.csv"');
    }

    public function exportarInventario(Request $request, Response $response): Response
    {
        $csv = $this->exportacionService->exportarInventario();
        
        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="inventario_' . date('Y-m-d') . '.csv"');
    }
}
