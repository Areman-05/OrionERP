<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;
use OrionERP\Models\Producto;
use OrionERP\Models\Cliente;

class ImportacionService
{
    private $db;
    private $productoModel;
    private $clienteModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->productoModel = new Producto();
        $this->clienteModel = new Cliente();
    }

    public function importarProductos(string $csvContent): array
    {
        $lines = str_getcsv($csvContent, "\n");
        $headers = str_getcsv(array_shift($lines));
        
        $importados = 0;
        $errores = [];
        
        foreach ($lines as $index => $line) {
            if (empty(trim($line))) {
                continue;
            }
            
            $data = str_getcsv($line);
            $row = array_combine($headers, $data);
            
            try {
                if (empty($row['codigo']) || empty($row['nombre'])) {
                    $errores[] = "Fila " . ($index + 2) . ": Código y nombre son requeridos";
                    continue;
                }
                
                // Verificar si existe
                $existente = $this->productoModel->findByCodigo($row['codigo']);
                
                if ($existente) {
                    // Actualizar
                    $this->productoModel->update($existente['id'], [
                        'nombre' => $row['nombre'],
                        'precio_venta' => $row['precio_venta'] ?? $existente['precio_venta'],
                        'precio_compra' => $row['precio_compra'] ?? $existente['precio_compra'],
                        'stock_actual' => $row['stock_actual'] ?? $existente['stock_actual']
                    ]);
                } else {
                    // Crear
                    $this->productoModel->create([
                        'codigo' => $row['codigo'],
                        'nombre' => $row['nombre'],
                        'precio_venta' => $row['precio_venta'] ?? 0,
                        'precio_compra' => $row['precio_compra'] ?? 0,
                        'stock_actual' => $row['stock_actual'] ?? 0
                    ]);
                }
                
                $importados++;
            } catch (\Exception $e) {
                $errores[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        return [
            'importados' => $importados,
            'errores' => $errores
        ];
    }

    public function importarClientes(string $csvContent): array
    {
        $lines = str_getcsv($csvContent, "\n");
        $headers = str_getcsv(array_shift($lines));
        
        $importados = 0;
        $errores = [];
        
        foreach ($lines as $index => $line) {
            if (empty(trim($line))) {
                continue;
            }
            
            $data = str_getcsv($line);
            $row = array_combine($headers, $data);
            
            try {
                if (empty($row['codigo']) || empty($row['nombre'])) {
                    $errores[] = "Fila " . ($index + 2) . ": Código y nombre son requeridos";
                    continue;
                }
                
                $existente = $this->clienteModel->findByCodigo($row['codigo']);
                
                if ($existente) {
                    $this->clienteModel->update($existente['id'], [
                        'nombre' => $row['nombre'],
                        'email' => $row['email'] ?? $existente['email'],
                        'telefono' => $row['telefono'] ?? $existente['telefono']
                    ]);
                } else {
                    $this->clienteModel->create([
                        'codigo' => $row['codigo'],
                        'nombre' => $row['nombre'],
                        'email' => $row['email'] ?? null,
                        'telefono' => $row['telefono'] ?? null
                    ]);
                }
                
                $importados++;
            } catch (\Exception $e) {
                $errores[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        
        return [
            'importados' => $importados,
            'errores' => $errores
        ];
    }
}

