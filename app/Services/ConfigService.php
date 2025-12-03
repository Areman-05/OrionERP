<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class ConfigService
{
    private $db;
    private $cache = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function get(string $key, $default = null)
    {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $config = $this->db->fetchOne(
            "SELECT valor FROM configuracion_empresa WHERE clave = ?",
            [$key]
        );

        if ($config) {
            $value = json_decode($config['valor'], true);
            $this->cache[$key] = $value;
            return $value;
        }

        return $default;
    }

    public function set(string $key, $value): void
    {
        $exists = $this->db->fetchOne(
            "SELECT id FROM configuracion_empresa WHERE clave = ?",
            [$key]
        );

        $jsonValue = json_encode($value);

        if ($exists) {
            $this->db->query(
                "UPDATE configuracion_empresa SET valor = ? WHERE clave = ?",
                [$jsonValue, $key]
            );
        } else {
            $this->db->query(
                "INSERT INTO configuracion_empresa (clave, valor) VALUES (?, ?)",
                [$key, $jsonValue]
            );
        }

        $this->cache[$key] = $value;
    }

    public function clearCache(): void
    {
        $this->cache = [];
    }
}

