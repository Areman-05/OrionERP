<?php

namespace OrionERP\Utils;

class DateHelper
{
    public static function formatearFecha(string $fecha, string $formato = 'd/m/Y'): string
    {
        $date = new \DateTime($fecha);
        return $date->format($formato);
    }

    public static function formatearFechaHora(string $fecha, string $formato = 'd/m/Y H:i'): string
    {
        $date = new \DateTime($fecha);
        return $date->format($formato);
    }

    public static function esFechaValida(string $fecha, string $formato = 'Y-m-d'): bool
    {
        $date = \DateTime::createFromFormat($formato, $fecha);
        return $date && $date->format($formato) === $fecha;
    }

    public static function agregarDias(string $fecha, int $dias): string
    {
        $date = new \DateTime($fecha);
        $date->modify("+$dias days");
        return $date->format('Y-m-d');
    }

    public static function diferenciaDias(string $fecha1, string $fecha2): int
    {
        $date1 = new \DateTime($fecha1);
        $date2 = new \DateTime($fecha2);
        $diff = $date1->diff($date2);
        return (int) $diff->format('%r%a');
    }

    public static function getPrimerDiaMes(string $fecha = 'now'): string
    {
        $date = new \DateTime($fecha);
        $date->modify('first day of this month');
        return $date->format('Y-m-d');
    }

    public static function getUltimoDiaMes(string $fecha = 'now'): string
    {
        $date = new \DateTime($fecha);
        $date->modify('last day of this month');
        return $date->format('Y-m-d');
    }
}
