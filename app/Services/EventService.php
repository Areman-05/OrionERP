<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class EventService
{
    private $db;
    private $listeners = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function registrarEvento(string $evento, array $datos, int $usuarioId = null): int
    {
        $this->db->query(
            "INSERT INTO eventos (evento, datos, usuario_id, fecha_creacion)
             VALUES (?, ?, ?, NOW())",
            [$evento, json_encode($datos), $usuarioId]
        );

        $eventoId = (int) $this->db->lastInsertId();

        // Disparar listeners locales
        $this->dispararListeners($evento, $datos);

        return $eventoId;
    }

    public function on(string $evento, callable $listener): void
    {
        if (!isset($this->listeners[$evento])) {
            $this->listeners[$evento] = [];
        }
        $this->listeners[$evento][] = $listener;
    }

    private function dispararListeners(string $evento, array $datos): void
    {
        if (isset($this->listeners[$evento])) {
            foreach ($this->listeners[$evento] as $listener) {
                call_user_func($listener, $datos);
            }
        }
    }

    public function getEventos(string $evento = null, int $limit = 50): array
    {
        $where = "1=1";
        $params = [];

        if ($evento) {
            $where .= " AND evento = ?";
            $params[] = $evento;
        }

        $params[] = $limit;

        return $this->db->fetchAll(
            "SELECT e.*, u.nombre as usuario_nombre
             FROM eventos e
             LEFT JOIN usuarios u ON e.usuario_id = u.id
             WHERE $where
             ORDER BY e.fecha_creacion DESC
             LIMIT ?",
            $params
        );
    }
}

