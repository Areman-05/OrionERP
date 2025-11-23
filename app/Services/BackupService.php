<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class BackupService
{
    private $db;
    private $backupDir;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->backupDir = __DIR__ . '/../../backups';
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function crearBackup(): string
    {
        $fecha = date('Y-m-d_H-i-s');
        $archivo = $this->backupDir . '/backup_' . $fecha . '.sql';
        
        $config = $this->getDatabaseConfig();
        $comando = sprintf(
            'mysqldump -u %s -p%s %s > %s',
            escapeshellarg($config['user']),
            escapeshellarg($config['password']),
            escapeshellarg($config['database']),
            escapeshellarg($archivo)
        );
        
        exec($comando, $output, $returnVar);
        
        if ($returnVar !== 0) {
            throw new \Exception('Error al crear backup de la base de datos');
        }
        
        return $archivo;
    }

    public function listarBackups(): array
    {
        $backups = [];
        $archivos = glob($this->backupDir . '/backup_*.sql');
        
        foreach ($archivos as $archivo) {
            $backups[] = [
                'archivo' => basename($archivo),
                'ruta' => $archivo,
                'tamaño' => filesize($archivo),
                'fecha' => date('Y-m-d H:i:s', filemtime($archivo))
            ];
        }
        
        usort($backups, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });
        
        return $backups;
    }

    public function restaurarBackup(string $archivo): bool
    {
        $rutaCompleta = $this->backupDir . '/' . $archivo;
        
        if (!file_exists($rutaCompleta)) {
            throw new \Exception('Archivo de backup no encontrado');
        }
        
        $config = $this->getDatabaseConfig();
        $comando = sprintf(
            'mysql -u %s -p%s %s < %s',
            escapeshellarg($config['user']),
            escapeshellarg($config['password']),
            escapeshellarg($config['database']),
            escapeshellarg($rutaCompleta)
        );
        
        exec($comando, $output, $returnVar);
        
        return $returnVar === 0;
    }

    public function eliminarBackup(string $archivo): bool
    {
        $rutaCompleta = $this->backupDir . '/' . $archivo;
        
        if (file_exists($rutaCompleta)) {
            return unlink($rutaCompleta);
        }
        
        return false;
    }

    private function getDatabaseConfig(): array
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        
        return [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'user' => $_ENV['DB_USER'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'database' => $_ENV['DB_NAME'] ?? 'orionerp'
        ];
    }
}

