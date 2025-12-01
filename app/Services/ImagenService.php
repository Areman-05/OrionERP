<?php

namespace OrionERP\Services;

class ImagenService
{
    private $uploadDir;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/productos/';
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function subirImagenProducto(array $file, int $productoId): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Error al subir la imagen');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $extensionesPermitidas)) {
            throw new \Exception('Formato de imagen no permitido');
        }

        $nombreArchivo = 'producto_' . $productoId . '_' . time() . '.' . $extension;
        $rutaCompleta = $this->uploadDir . $nombreArchivo;

        if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
            throw new \Exception('Error al mover la imagen');
        }

        // Redimensionar si es necesario
        $this->redimensionarImagen($rutaCompleta, 800, 800);

        return str_replace(__DIR__ . '/../../public/', '', $rutaCompleta);
    }

    private function redimensionarImagen(string $ruta, int $anchoMax, int $altoMax): void
    {
        $info = getimagesize($ruta);
        if (!$info) {
            return;
        }

        $ancho = $info[0];
        $alto = $info[1];
        $tipo = $info[2];

        if ($ancho <= $anchoMax && $alto <= $altoMax) {
            return;
        }

        $ratio = min($anchoMax / $ancho, $altoMax / $alto);
        $nuevoAncho = (int) ($ancho * $ratio);
        $nuevoAlto = (int) ($alto * $ratio);

        $imagen = match($tipo) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($ruta),
            IMAGETYPE_PNG => imagecreatefrompng($ruta),
            IMAGETYPE_GIF => imagecreatefromgif($ruta),
            default => null
        };

        if (!$imagen) {
            return;
        }

        $nuevaImagen = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        
        if ($tipo === IMAGETYPE_PNG || $tipo === IMAGETYPE_GIF) {
            imagealphablending($nuevaImagen, false);
            imagesavealpha($nuevaImagen, true);
        }

        imagecopyresampled($nuevaImagen, $imagen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

        match($tipo) {
            IMAGETYPE_JPEG => imagejpeg($nuevaImagen, $ruta, 85),
            IMAGETYPE_PNG => imagepng($nuevaImagen, $ruta),
            IMAGETYPE_GIF => imagegif($nuevaImagen, $ruta),
            default => null
        };

        imagedestroy($imagen);
        imagedestroy($nuevaImagen);
    }

    public function eliminarImagen(string $ruta): bool
    {
        $rutaCompleta = __DIR__ . '/../../public/' . $ruta;
        
        if (file_exists($rutaCompleta)) {
            return unlink($rutaCompleta);
        }
        
        return false;
    }
}

