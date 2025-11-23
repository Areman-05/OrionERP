<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class DocumentoCliente
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByCliente(int $clienteId): array
    {
        return $this->db->fetchAll(
            "SELECT d.*, u.nombre as usuario_nombre 
             FROM documentos_cliente d
             LEFT JOIN usuarios u ON d.usuario_id = u.id
             WHERE d.cliente_id = ?
             ORDER BY d.created_at DESC",
            [$clienteId]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT d.*, c.nombre as cliente_nombre, u.nombre as usuario_nombre 
             FROM documentos_cliente d
             LEFT JOIN clientes c ON d.cliente_id = c.id
             LEFT JOIN usuarios u ON d.usuario_id = u.id
             WHERE d.id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO documentos_cliente (cliente_id, tipo, nombre, archivo, descripcion, fecha_documento, usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['cliente_id'],
            $data['tipo'],
            $data['nombre'],
            $data['archivo'],
            $data['descripcion'] ?? null,
            $data['fecha_documento'] ?? null,
            $data['usuario_id'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $documento = $this->findById($id);
        if ($documento && file_exists($documento['archivo'])) {
            unlink($documento['archivo']);
        }
        
        $this->db->query("DELETE FROM documentos_cliente WHERE id = ?", [$id]);
        return true;
    }
}

