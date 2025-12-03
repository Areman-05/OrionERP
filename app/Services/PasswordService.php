<?php

namespace OrionERP\Services;

class PasswordService
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function generarPasswordAleatoria(int $longitud = 12): string
    {
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $longitud; $i++) {
            $password .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
        
        return $password;
    }

    public function validarFortaleza(string $password): array
    {
        $errores = [];
        
        if (strlen($password) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errores[] = 'La contraseña debe contener al menos una letra mayúscula';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errores[] = 'La contraseña debe contener al menos una letra minúscula';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errores[] = 'La contraseña debe contener al menos un número';
        }
        
        return [
            'valida' => empty($errores),
            'errores' => $errores
        ];
    }
}

