<?php

namespace OrionERP\Services;

use OrionERP\Models\Producto;
use OrionERP\Models\VarianteProducto;
use OrionERP\Models\Etiqueta;

class ProductoService
{
    private $productoModel;
    private $varianteModel;
    private $etiquetaModel;

    public function __construct()
    {
        $this->productoModel = new Producto();
        $this->varianteModel = new VarianteProducto();
        $this->etiquetaModel = new Etiqueta();
    }

    public function crearProductoCompleto(array $data): int
    {
        $productoId = $this->productoModel->create($data);
        
        // Crear variantes si existen
        if (!empty($data['variantes']) && is_array($data['variantes'])) {
            foreach ($data['variantes'] as $variante) {
                $variante['producto_id'] = $productoId;
                $this->varianteModel->create($variante);
            }
        }
        
        // Agregar etiquetas si existen
        if (!empty($data['etiquetas']) && is_array($data['etiquetas'])) {
            foreach ($data['etiquetas'] as $etiquetaId) {
                $this->etiquetaModel->agregarAProducto($productoId, $etiquetaId);
            }
        }
        
        return $productoId;
    }

    public function getProductoCompleto(int $productoId): ?array
    {
        $producto = $this->productoModel->findById($productoId);
        
        if (!$producto) {
            return null;
        }
        
        $producto['variantes'] = $this->varianteModel->getByProducto($productoId);
        $producto['etiquetas'] = $this->etiquetaModel->getByProducto($productoId);
        
        return $producto;
    }

    public function buscarProductosAvanzado(array $filtros): array
    {
        $productos = $this->productoModel->getAll();
        
        // Filtrar por etiquetas
        if (!empty($filtros['etiquetas'])) {
            $productos = array_filter($productos, function($producto) use ($filtros) {
                $etiquetas = $this->etiquetaModel->getByProducto($producto['id']);
                $etiquetasIds = array_column($etiquetas, 'id');
                return !empty(array_intersect($filtros['etiquetas'], $etiquetasIds));
            });
        }
        
        return array_values($productos);
    }
}

