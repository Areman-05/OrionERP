<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class AtributoProducto
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT a.* FROM atributos_producto a WHERE a.activo = 1 ORDER BY a.nombre"
        );
    }

    public function findById(int $id): ?array
    {
        $atributo = $this->db->fetchOne(
            "SELECT * FROM atributos_producto WHERE id = ?",
            [$id]
        );

        if ($atributo) {
            $atributo['valores'] = $this->getValores($id);
        }

        return $atributo;
    }

    public function getValores(int $atributoId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM valores_atributo WHERE atributo_id = ? ORDER BY orden, valor",
            [$atributoId]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO atributos_producto (nombre, tipo, activo) VALUES (?, ?, ?)";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['tipo'] ?? 'texto',
            $data['activo'] ?? 1
        ]);

        $atributoId = (int) $this->db->lastInsertId();

        // Agregar valores si existen
        if (!empty($data['valores']) && is_array($data['valores'])) {
            foreach ($data['valores'] as $orden => $valor) {
                $this->agregarValor($atributoId, $valor, $orden);
            }
        }

        return $atributoId;
    }

    public function agregarValor(int $atributoId, string $valor, int $orden = 0): int
    {
        $sql = "INSERT INTO valores_atributo (atributo_id, valor, orden) VALUES (?, ?, ?)";
        
        $this->db->query($sql, [$atributoId, $valor, $orden]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE atributos_producto SET nombre = ?, tipo = ?, activo = ? WHERE id = ?";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['tipo'] ?? 'texto',
            $data['activo'] ?? 1,
            $id
        ]);

        return true;
    }
}

