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

    public static function esDiaHabil(string $fecha): bool
    {
        $date = new \DateTime($fecha);
        $diaSemana = (int) $date->format('w');
        // 0 = domingo, 6 = sábado
        return $diaSemana > 0 && $diaSemana < 6;
    }

    public static function getRangoFechas(string $fechaInicio, string $fechaFin): array
    {
        $inicio = new \DateTime($fechaInicio);
        $fin = new \DateTime($fechaFin);
        $fechas = [];

        while ($inicio <= $fin) {
            $fechas[] = $inicio->format('Y-m-d');
            $inicio->modify('+1 day');
        }

        return $fechas;
    }

    public static function formatearFechaRelativa(string $fecha): string
    {
        $date = new \DateTime($fecha);
        $ahora = new \DateTime();
        $diff = $date->diff($ahora);

        if ($diff->days == 0) {
            return 'Hoy';
        } elseif ($diff->days == 1) {
            return 'Ayer';
        } elseif ($diff->days < 7) {
            return "Hace {$diff->days} días";
        } elseif ($diff->days < 30) {
            $semanas = floor($diff->days / 7);
            return "Hace {$semanas} " . ($semanas == 1 ? 'semana' : 'semanas');
        } elseif ($diff->days < 365) {
            $meses = floor($diff->days / 30);
            return "Hace {$meses} " . ($meses == 1 ? 'mes' : 'meses');
        } else {
            $anos = floor($diff->days / 365);
            return "Hace {$anos} " . ($anos == 1 ? 'año' : 'años');
        }
    }

    public static function getMesesAnio(): array
    {
        return [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
    }

    public static function getDiasSemana(): array
    {
        return [
            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'
        ];
    }

    public static function esFinDeSemana(string $fecha): bool
    {
        $date = new \DateTime($fecha);
        $diaSemana = (int) $date->format('w');
        return $diaSemana == 0 || $diaSemana == 6;
    }
}
