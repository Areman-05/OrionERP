<?php

namespace OrionERP\Models;

use OrionERP\Core\Database;

class ConfiguracionEmpresa
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        $configs = $this->db->fetchAll("SELECT * FROM configuracion_empresa ORDER BY clave");
        $resultado = [];
        
        foreach ($configs as $config) {
            $valor = $config['valor'];
            
            switch ($config['tipo']) {
                case 'numero':
                    $valor = (float) $valor;
                    break;
                case 'booleano':
                    $valor = (bool) $valor;
                    break;
                case 'json':
                    $valor = json_decode($valor, true);
                    break;
            }
            
            $resultado[$config['clave']] = $valor;
        }
        
        return $resultado;
    }

    public function get(string $clave, $default = null)
    {
        $config = $this->db->fetchOne(
            "SELECT * FROM configuracion_empresa WHERE clave = ?",
            [$clave]
        );
        
        if (!$config) {
            return $default;
        }
        
        $valor = $config['valor'];
        
        switch ($config['tipo']) {
            case 'numero':
                return (float) $valor;
            case 'booleano':
                return (bool) $valor;
            case 'json':
                return json_decode($valor, true);
            default:
                return $valor;
        }
    }

    public function set(string $clave, $valor, string $tipo = 'texto'): bool
    {
        if ($tipo === 'json') {
            $valor = json_encode($valor);
        } elseif ($tipo === 'booleano') {
            $valor = $valor ? '1' : '0';
        } else {
            $valor = (string) $valor;
        }
        
        $existe = $this->db->fetchOne(
            "SELECT id FROM configuracion_empresa WHERE clave = ?",
            [$clave]
        );
        
        if ($existe) {
            $this->db->query(
                "UPDATE configuracion_empresa SET valor = ?, tipo = ? WHERE clave = ?",
                [$valor, $tipo, $clave]
            );
        } else {
            $this->db->query(
                "INSERT INTO configuracion_empresa (clave, valor, tipo) VALUES (?, ?, ?)",
                [$clave, $valor, $tipo]
            );
        }
        
        return true;
    }
}

