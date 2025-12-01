<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\Proveedor;
use OrionERP\Services\ProveedorService;

class ProveedorController
{
    private $proveedorModel;
    private $proveedorService;

    public function __construct()
    {
        $this->proveedorModel = new Proveedor();
        $this->proveedorService = new ProveedorService();
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $proveedor = $this->proveedorService->getProveedorCompleto($id);
        
        if (!$proveedor) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $proveedor
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}
