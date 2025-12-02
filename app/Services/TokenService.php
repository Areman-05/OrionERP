<?php

namespace OrionERP\Services;

class TokenService
{
    public function generarTokenAleatorio(int $longitud = 32): string
    {
        return bin2hex(random_bytes($longitud));
    }

    public function generarTokenCSRF(): string
    {
        return $this->generarTokenAleatorio(32);
    }

    public function generarTokenRecuperacion(): string
    {
        return $this->generarTokenAleatorio(64);
    }

    public function validarTokenCSRF(string $token, string $tokenSesion): bool
    {
        return hash_equals($tokenSesion, $token);
    }
}

