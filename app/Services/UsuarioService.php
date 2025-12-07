<?php

namespace OrionERP\Services;

use OrionERP\Models\Usuario;
use OrionERP\Core\Database;
use OrionERP\Services\PasswordService;

class UsuarioService
{
    private $usuarioModel;
    private $db;
    private $passwordService;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->db = Database::getInstance();
        $this->passwordService = new PasswordService();
    }

    public function getActividadUsuario(int $usuarioId, int $dias = 30): array
    {
        $fechaInicio = date('Y-m-d', strtotime("-$dias days"));
        
        $pedidos = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_venta 
             WHERE usuario_id = ? AND fecha >= ?",
            [$usuarioId, $fechaInicio]
        );

        $facturas = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM facturas 
             WHERE usuario_id = ? AND fecha_emision >= ?",
            [$usuarioId, $fechaInicio]
        );

        $logs = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM logs 
             WHERE usuario_id = ? AND DATE(created_at) >= ?",
            [$usuarioId, $fechaInicio]
        );

        return [
            'pedidos' => (int) ($pedidos['total'] ?? 0),
            'facturas' => (int) ($facturas['total'] ?? 0),
            'acciones' => (int) ($logs['total'] ?? 0)
        ];
    }

    public function cambiarPassword(int $usuarioId, string $passwordActual, string $passwordNueva): bool
    {
        $usuario = $this->usuarioModel->findById($usuarioId);
        if (!$usuario) {
            return false;
        }

        $usuarioCompleto = $this->db->fetchOne(
            "SELECT password FROM usuarios WHERE id = ?",
            [$usuarioId]
        );

        if (!$this->passwordService->verify($passwordActual, $usuarioCompleto['password'])) {
            return false;
        }

        $validacion = $this->passwordService->validarFortaleza($passwordNueva);
        if (!$validacion['valida']) {
            throw new \Exception('La contraseña no cumple con los requisitos de seguridad');
        }

        $hash = $this->passwordService->hash($passwordNueva);
        return $this->usuarioModel->update($usuarioId, ['password' => $hash]);
    }

    public function getUsuariosPorRol(string $rol): array
    {
        return $this->db->fetchAll(
            "SELECT id, nombre, email, rol, activo, ultimo_acceso 
             FROM usuarios 
             WHERE rol = ? AND activo = 1
             ORDER BY nombre ASC",
            [$rol]
        );
    }
}
