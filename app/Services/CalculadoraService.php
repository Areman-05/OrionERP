<?php

namespace OrionERP\Services;

class CalculadoraService
{
    public function calcularPrecioVenta(float $precioCompra, float $margenPorcentaje): float
    {
        return $precioCompra * (1 + ($margenPorcentaje / 100));
    }

    public function calcularMargen(float $precioVenta, float $precioCompra): float
    {
        if ($precioCompra == 0) {
            return 0;
        }
        
        return (($precioVenta - $precioCompra) / $precioCompra) * 100;
    }

    public function calcularDescuento(float $precioOriginal, float $precioFinal): float
    {
        if ($precioOriginal == 0) {
            return 0;
        }
        
        return (($precioOriginal - $precioFinal) / $precioOriginal) * 100;
    }

    public function calcularTotalConIVA(float $base, float $iva = 21): float
    {
        return $base * (1 + ($iva / 100));
    }

    public function calcularBaseDesdeTotal(float $total, float $iva = 21): float
    {
        return $total / (1 + ($iva / 100));
    }
}

