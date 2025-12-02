<?php

namespace OrionERP\Services;

use OrionERP\Models\Usuario;
use OrionERP\Core\Database;

class UsuarioService
{
    private $usuarioModel;
    private $db;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->db = Database::getInstance();
    }

    public function crearUsuario(array $data): int
    {
        // Validar email único
        $existe = $this->usuarioModel->findByEmail($data['email']);
        if ($existe) {
            throw new \Exception('El email ya está en uso');
        }

        return $this->usuarioModel->create($data);
    }

    public function actualizarUltimoAcceso(int $usuarioId): void
    {
        $this->usuarioModel->updateLastAccess($usuarioId);
    }

    public function cambiarPassword(int $usuarioId, string $passwordActual, string $passwordNuevo): bool
    {
        $usuario = $this->usuarioModel->findById($usuarioId);
        if (!$usuario) {
            throw new \Exception('Usuario no encontrado');
        }

        // Obtener hash de la base de datos
        $usuarioCompleto = $this->db->fetchOne(
            "SELECT password FROM usuarios WHERE id = ?",
            [$usuarioId]
        );

        if (!password_verify($passwordActual, $usuarioCompleto['password'])) {
            throw new \Exception('Contraseña actual incorrecta');
        }

        return $this->usuarioModel->update($usuarioId, ['password' => $passwordNuevo]);
    }

    public function getEstadisticasUsuario(int $usuarioId): array
    {
        $pedidos = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM pedidos_venta WHERE usuario_id = ?",
            [$usuarioId]
        );

        $ventas = $this->db->fetchOne(
            "SELECT SUM(total) as total FROM pedidos_venta 
             WHERE usuario_id = ? AND estado != 'cancelado'",
            [$usuarioId]
        );

        return [
            'total_pedidos' => (int) ($pedidos['total'] ?? 0),
            'total_ventas' => (float) ($ventas['total'] ?? 0)
        ];
    }
}

