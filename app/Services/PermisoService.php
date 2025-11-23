<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class PermisoService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function tienePermiso(string $rol, string $modulo, string $permiso): bool
    {
        // Admin tiene todos los permisos
        if ($rol === 'admin') {
            return true;
        }
        
        $resultado = $this->db->fetchOne(
            "SELECT id FROM permisos_modulo WHERE rol = ? AND modulo = ? AND permiso = ? AND activo = 1",
            [$rol, $modulo, $permiso]
        );
        
        return $resultado !== null;
    }

    public function getPermisosRol(string $rol): array
    {
        $permisos = $this->db->fetchAll(
            "SELECT modulo, permiso FROM permisos_modulo WHERE rol = ? AND activo = 1",
            [$rol]
        );
        
        $resultado = [];
        foreach ($permisos as $permiso) {
            if (!isset($resultado[$permiso['modulo']])) {
                $resultado[$permiso['modulo']] = [];
            }
            $resultado[$permiso['modulo']][] = $permiso['permiso'];
        }
        
        return $resultado;
    }
}

