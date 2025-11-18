<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM usuarios WHERE email = ? AND activo = 1",
            [$email]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT id, nombre, email, rol, activo, ultimo_acceso, created_at FROM usuarios WHERE id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo) 
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['nombre'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['rol'] ?? 'empleado',
            $data['activo'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];

        if (isset($data['nombre'])) {
            $fields[] = "nombre = ?";
            $params[] = $data['nombre'];
        }
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (isset($data['rol'])) {
            $fields[] = "rol = ?";
            $params[] = $data['rol'];
        }
        if (isset($data['activo'])) {
            $fields[] = "activo = ?";
            $params[] = $data['activo'];
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE id = ?";
        
        return $this->db->execute($sql, $params);
    }

    public function updateLastAccess(int $id): bool
    {
        return $this->db->execute(
            "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?",
            [$id]
        );
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT id, nombre, email, rol, activo, ultimo_acceso, created_at 
             FROM usuarios ORDER BY nombre ASC"
        );
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

