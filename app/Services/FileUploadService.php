<?php

namespace OrionERP\Services;

class FileUploadService
{
    private $uploadDir;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/';
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function subirArchivo(array $file, string $subdirectorio = '', array $tiposPermitidos = ['jpg', 'jpeg', 'png', 'pdf']): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Error al subir el archivo');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $tiposPermitidos)) {
            throw new \Exception('Tipo de archivo no permitido');
        }

        $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
        $directorioDestino = $this->uploadDir . ($subdirectorio ? $subdirectorio . '/' : '');
        
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
            throw new \Exception('Error al mover el archivo');
        }

        return [
            'nombre' => $nombreArchivo,
            'ruta' => str_replace(__DIR__ . '/../../public/', '', $rutaCompleta),
            'tamaño' => filesize($rutaCompleta),
            'tipo' => $file['type']
        ];
    }

    public function eliminarArchivo(string $ruta): bool
    {
        $rutaCompleta = __DIR__ . '/../../public/' . $ruta;
        
        if (file_exists($rutaCompleta)) {
            return unlink($rutaCompleta);
        }
        
        return false;
    }
}

