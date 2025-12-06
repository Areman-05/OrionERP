<?php

namespace OrionERP\Services;

class ApiDocumentationService
{
    public function generarDocumentacion(): array
    {
        return [
            'version' => '1.0.0',
            'endpoints' => [
                'autenticacion' => [
                    'POST /api/auth/login' => 'Iniciar sesión',
                    'POST /api/auth/refresh' => 'Renovar token'
                ],
                'productos' => [
                    'GET /api/productos' => 'Listar productos',
                    'GET /api/productos/{id}' => 'Obtener producto',
                    'POST /api/productos' => 'Crear producto',
                    'PUT /api/productos/{id}' => 'Actualizar producto',
                    'DELETE /api/productos/{id}' => 'Eliminar producto'
                ],
                'clientes' => [
                    'GET /api/clientes' => 'Listar clientes',
                    'GET /api/clientes/{id}' => 'Obtener cliente',
                    'POST /api/clientes' => 'Crear cliente',
                    'PUT /api/clientes/{id}' => 'Actualizar cliente'
                ],
                'pedidos' => [
                    'GET /api/pedidos' => 'Listar pedidos',
                    'GET /api/pedidos/{id}' => 'Obtener pedido',
                    'POST /api/pedidos' => 'Crear pedido'
                ],
                'facturas' => [
                    'GET /api/facturas' => 'Listar facturas',
                    'GET /api/facturas/{id}' => 'Obtener factura',
                    'POST /api/facturas' => 'Crear factura'
                ]
            ],
            'autenticacion' => [
                'tipo' => 'Bearer Token',
                'header' => 'Authorization: Bearer {token}'
            ]
        ];
    }

    public function getEndpointInfo(string $method, string $path): ?array
    {
        $documentacion = $this->generarDocumentacion();
        
        foreach ($documentacion['endpoints'] as $categoria => $endpoints) {
            foreach ($endpoints as $endpoint => $descripcion) {
                if ($this->matchEndpoint($method, $path, $endpoint)) {
                    return [
                        'categoria' => $categoria,
                        'endpoint' => $endpoint,
                        'descripcion' => $descripcion
                    ];
                }
            }
        }
        
        return null;
    }

    private function matchEndpoint(string $method, string $path, string $pattern): bool
    {
        $pattern = str_replace('{id}', '(\d+)', $pattern);
        $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';
        
        return preg_match($pattern, $path);
    }
}

