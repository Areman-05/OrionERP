<?php

namespace OrionERP\Services;

class SerializacionService
{
    public function serializarProducto(array $producto): array
    {
        return [
            'id' => $producto['id'],
            'codigo' => $producto['codigo'],
            'nombre' => $producto['nombre'],
            'precio_venta' => (float) $producto['precio_venta'],
            'stock_actual' => (int) $producto['stock_actual'],
            'categoria' => $producto['categoria_nombre'] ?? null
        ];
    }

    public function serializarCliente(array $cliente): array
    {
        return [
            'id' => $cliente['id'],
            'codigo' => $cliente['codigo'],
            'nombre' => $cliente['nombre'],
            'email' => $cliente['email'] ?? null,
            'telefono' => $cliente['telefono'] ?? null,
            'estado' => $cliente['estado']
        ];
    }

    public function serializarPedido(array $pedido): array
    {
        return [
            'id' => $pedido['id'],
            'numero_pedido' => $pedido['numero_pedido'],
            'cliente' => $pedido['cliente_nombre'] ?? null,
            'fecha' => $pedido['fecha'],
            'estado' => $pedido['estado'],
            'total' => (float) $pedido['total']
        ];
    }
}

