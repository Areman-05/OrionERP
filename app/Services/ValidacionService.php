<?php

namespace OrionERP\Services;

use OrionERP\Utils\Validator;

class ValidacionService
{
    public function validarProducto(array $data): array
    {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores['nombre'] = 'El nombre es requerido';
        }

        if (empty($data['codigo'])) {
            $errores['codigo'] = 'El código es requerido';
        }

        if (isset($data['precio_venta']) && !Validator::numeric($data['precio_venta'])) {
            $errores['precio_venta'] = 'El precio de venta debe ser numérico';
        }

        if (isset($data['precio_venta']) && $data['precio_venta'] < 0) {
            $errores['precio_venta'] = 'El precio de venta no puede ser negativo';
        }

        return $errores;
    }

    public function validarCliente(array $data): array
    {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores['nombre'] = 'El nombre es requerido';
        }

        if (empty($data['codigo'])) {
            $errores['codigo'] = 'El código es requerido';
        }

        if (!empty($data['email']) && !Validator::email($data['email'])) {
            $errores['email'] = 'El email no es válido';
        }

        return $errores;
    }

    public function validarPedido(array $data): array
    {
        $errores = [];

        if (empty($data['cliente_id'])) {
            $errores['cliente_id'] = 'El cliente es requerido';
        }

        if (empty($data['lineas']) || !is_array($data['lineas']) || count($data['lineas']) === 0) {
            $errores['lineas'] = 'Debe agregar al menos una línea al pedido';
        }

        return $errores;
    }

    public function validarFactura(array $data): array
    {
        $errores = [];

        if (empty($data['cliente_id'])) {
            $errores['cliente_id'] = 'El cliente es requerido';
        }

        if (empty($data['fecha_emision'])) {
            $errores['fecha_emision'] = 'La fecha de emisión es requerida';
        }

        if (empty($data['lineas']) || !is_array($data['lineas']) || count($data['lineas']) === 0) {
            $errores['lineas'] = 'Debe agregar al menos una línea a la factura';
        }

        if (isset($data['fecha_vencimiento']) && $data['fecha_vencimiento'] < $data['fecha_emision']) {
            $errores['fecha_vencimiento'] = 'La fecha de vencimiento no puede ser anterior a la fecha de emisión';
        }

        return $errores;
    }

    public function validarUsuario(array $data): array
    {
        $errores = [];

        if (empty($data['nombre'])) {
            $errores['nombre'] = 'El nombre es requerido';
        }

        if (empty($data['email'])) {
            $errores['email'] = 'El email es requerido';
        } elseif (!Validator::email($data['email'])) {
            $errores['email'] = 'El email no es válido';
        }

        if (isset($data['password']) && strlen($data['password']) < 8) {
            $errores['password'] = 'La contraseña debe tener al menos 8 caracteres';
        }

        if (isset($data['rol']) && !in_array($data['rol'], ['admin', 'empleado', 'vendedor'])) {
            $errores['rol'] = 'El rol no es válido';
        }

        return $errores;
    }
}

