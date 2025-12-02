<?php

namespace OrionERP\Utils;

class StringHelper
{
    public static function limpiar(string $texto): string
    {
        return trim(strip_tags($texto));
    }

    public static function truncar(string $texto, int $longitud = 100, string $sufijo = '...'): string
    {
        if (mb_strlen($texto) <= $longitud) {
            return $texto;
        }
        
        return mb_substr($texto, 0, $longitud) . $sufijo;
    }

    public static function generarCodigo(string $prefijo = '', int $longitud = 8): string
    {
        $codigo = $prefijo . strtoupper(substr(md5(uniqid(rand(), true)), 0, $longitud));
        return $codigo;
    }

    public static function slug(string $texto): string
    {
        $texto = mb_strtolower($texto);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        return trim($texto, '-');
    }

    public static function capitalizar(string $texto): string
    {
        return mb_convert_case($texto, MB_CASE_TITLE, 'UTF-8');
    }

    public static function enmascararEmail(string $email): string
    {
        $partes = explode('@', $email);
        if (count($partes) !== 2) {
            return $email;
        }
        
        $usuario = $partes[0];
        $dominio = $partes[1];
        
        $usuarioEnmascarado = substr($usuario, 0, 2) . str_repeat('*', max(0, strlen($usuario) - 2));
        
        return $usuarioEnmascarado . '@' . $dominio;
    }
}


