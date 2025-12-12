<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class ContactoService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function registrarContacto(string $tipo, int $entidadId, string $tipoContacto, string $valor, string $notas = null): int
    {
        $this->db->query(
            "INSERT INTO contactos (tipo_entidad, entidad_id, tipo_contacto, valor, notas, fecha_creacion)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [$tipo, $entidadId, $tipoContacto, $valor, $notas]
        );

        return (int) $this->db->lastInsertId();
    }

    public function getContactosPorEntidad(string $tipo, int $entidadId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM contactos 
             WHERE tipo_entidad = ? AND entidad_id = ?
             ORDER BY tipo_contacto, fecha_creacion DESC",
            [$tipo, $entidadId]
        );
    }

    public function actualizarContacto(int $contactoId, array $datos): bool
    {
        $campos = [];
        $valores = [];

        foreach ($datos as $campo => $valor) {
            if (in_array($campo, ['tipo_contacto', 'valor', 'notas'])) {
                $campos[] = "$campo = ?";
                $valores[] = $valor;
            }
        }

        if (empty($campos)) {
            return false;
        }

        $valores[] = $contactoId;

        return $this->db->query(
            "UPDATE contactos SET " . implode(', ', $campos) . " WHERE id = ?",
            $valores
        );
    }

    public function eliminarContacto(int $contactoId): bool
    {
        return $this->db->query(
            "DELETE FROM contactos WHERE id = ?",
            [$contactoId]
        );
    }
}

