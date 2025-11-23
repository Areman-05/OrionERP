<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Services\BuscadorService;

class BuscadorController
{
    private $buscadorService;

    public function __construct()
    {
        $this->buscadorService = new BuscadorService();
    }

    public function buscarProductos(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $filtros = [
            'texto' => $queryParams['q'] ?? null,
            'categoria_id' => $queryParams['categoria_id'] ?? null,
            'precio_min' => $queryParams['precio_min'] ?? null,
            'precio_max' => $queryParams['precio_max'] ?? null,
            'stock_minimo' => isset($queryParams['stock_minimo']) ? (bool) $queryParams['stock_minimo'] : null,
            'limit' => $queryParams['limit'] ?? 50
        ];
        
        $resultados = $this->buscadorService->buscarProductos($filtros);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $resultados,
            'total' => count($resultados)
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function autocompletar(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $termino = $queryParams['q'] ?? '';
        
        $resultados = $this->buscadorService->autocompletar($termino);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $resultados
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function buscarClientes(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $filtros = [
            'texto' => $queryParams['q'] ?? null,
            'estado' => $queryParams['estado'] ?? null
        ];
        
        $resultados = $this->buscadorService->buscarClientes($filtros);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $resultados,
            'total' => count($resultados)
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

