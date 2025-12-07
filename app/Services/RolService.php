<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class RolService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getPermisosPorRol(string $rol): array
    {
        return $this->db->fetchAll(
            "SELECT p.* FROM permisos p
             INNER JOIN roles_permisos rp ON p.id = rp.permiso_id
             INNER JOIN roles r ON rp.rol_id = r.id
             WHERE r.nombre = ?",
            [$rol]
        );
    }

    public function asignarPermisosARol(string $rol, array $permisos): bool
    {
        $rolData = $this->db->fetchOne(
            "SELECT id FROM roles WHERE nombre = ?",
            [$rol]
        );

        if (!$rolData) {
            return false;
        }

        $rolId = $rolData['id'];
        $this->db->beginTransaction();

        try {
            // Eliminar permisos existentes
            $this->db->query(
                "DELETE FROM roles_permisos WHERE rol_id = ?",
                [$rolId]
            );

            // Asignar nuevos permisos
            foreach ($permisos as $permisoId) {
                $this->db->query(
                    "INSERT INTO roles_permisos (rol_id, permiso_id) VALUES (?, ?)",
                    [$rolId, $permisoId]
                );
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getEstadisticasRoles(): array
    {
        return $this->db->fetchAll(
            "SELECT r.nombre, COUNT(u.id) as total_usuarios
             FROM roles r
             LEFT JOIN usuarios u ON r.nombre = u.rol AND u.activo = 1
             GROUP BY r.id, r.nombre
             ORDER BY total_usuarios DESC"
        );
    }
}

