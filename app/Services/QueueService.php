<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class QueueService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function agregarTarea(string $tipo, array $datos, int $prioridad = 0): int
    {
        $this->db->query(
            "INSERT INTO cola_tareas (tipo, datos, prioridad, estado, fecha_creacion)
             VALUES (?, ?, ?, 'pendiente', NOW())",
            [$tipo, json_encode($datos), $prioridad]
        );

        return (int) $this->db->lastInsertId();
    }

    public function obtenerSiguienteTarea(): ?array
    {
        $tarea = $this->db->fetchOne(
            "SELECT * FROM cola_tareas 
             WHERE estado = 'pendiente'
             ORDER BY prioridad DESC, fecha_creacion ASC
             LIMIT 1"
        );

        if ($tarea) {
            $this->db->query(
                "UPDATE cola_tareas SET estado = 'procesando', fecha_inicio = NOW() WHERE id = ?",
                [$tarea['id']]
            );
            $tarea['datos'] = json_decode($tarea['datos'], true);
        }

        return $tarea;
    }

    public function completarTarea(int $tareaId, string $resultado = null): bool
    {
        return $this->db->query(
            "UPDATE cola_tareas 
             SET estado = 'completada', resultado = ?, fecha_fin = NOW()
             WHERE id = ?",
            [$resultado, $tareaId]
        );
    }

    public function fallarTarea(int $tareaId, string $error): bool
    {
        return $this->db->query(
            "UPDATE cola_tareas 
             SET estado = 'fallida', error = ?, fecha_fin = NOW()
             WHERE id = ?",
            [$error, $tareaId]
        );
    }

    public function getTareasPendientes(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM cola_tareas 
             WHERE estado = 'pendiente'
             ORDER BY prioridad DESC, fecha_creacion ASC"
        );
    }
}

