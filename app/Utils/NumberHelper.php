<?php

namespace OrionERP\Utils;

class NumberHelper
{
    public static function formatearMoneda(float $cantidad, string $moneda = 'EUR'): string
    {
        $simbolos = [
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£'
        ];
        
        $simbolo = $simbolos[$moneda] ?? $moneda;
        
        return number_format($cantidad, 2, ',', '.') . ' ' . $simbolo;
    }

    public static function formatearNumero(float $numero, int $decimales = 2): string
    {
        return number_format($numero, $decimales, ',', '.');
    }

    public static function redondear(float $numero, int $decimales = 2): float
    {
        return round($numero, $decimales);
    }

    public static function calcularPorcentaje(float $valor, float $total): float
    {
        if ($total == 0) {
            return 0;
        }
        
        return ($valor / $total) * 100;
    }

    public static function aplicarDescuento(float $precio, float $descuento): float
    {
        return $precio * (1 - ($descuento / 100));
    }

    public static function calcularIVA(float $base, float $iva = 21): float
    {
        return $base * ($iva / 100);
    }
}


