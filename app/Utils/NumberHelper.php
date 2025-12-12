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

    public static function calcularTotalConIVA(float $base, float $iva = 21): float
    {
        return $base + self::calcularIVA($base, $iva);
    }

    public static function calcularBaseDesdeTotal(float $total, float $iva = 21): float
    {
        return $total / (1 + ($iva / 100));
    }

    public static function formatearPorcentaje(float $porcentaje, int $decimales = 2): string
    {
        return number_format($porcentaje, $decimales, ',', '.') . '%';
    }

    public static function esNumeroPositivo(float $numero): bool
    {
        return $numero > 0;
    }

    public static function formatearCantidad(float $cantidad, string $unidad = ''): string
    {
        $formateado = number_format($cantidad, 2, ',', '.');
        return $unidad ? "{$formateado} {$unidad}" : $formateado;
    }

    public static function formatearNumeroEntero(int $numero): string
    {
        return number_format($numero, 0, ',', '.');
    }

    public static function convertirStringANumero(string $valor): float
    {
        $valor = str_replace(['.', ','], ['', '.'], $valor);
        return (float) $valor;
    }

    public static function esPar(int $numero): bool
    {
        return $numero % 2 === 0;
    }

    public static function esImpar(int $numero): bool
    {
        return $numero % 2 !== 0;
    }
}


