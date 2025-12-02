<?php

namespace OrionERP\Services;

class EncriptacionService
{
    private $key;
    private $cipher = 'AES-256-CBC';

    public function __construct()
    {
        $this->key = $_ENV['ENCRYPTION_KEY'] ?? hash('sha256', 'default_key_change_in_production');
    }

    public function encriptar(string $texto): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $encriptado = openssl_encrypt($texto, $this->cipher, $this->key, 0, $iv);
        return base64_encode($encriptado . '::' . $iv);
    }

    public function desencriptar(string $textoEncriptado): string
    {
        list($encriptado, $iv) = explode('::', base64_decode($textoEncriptado), 2);
        return openssl_decrypt($encriptado, $this->cipher, $this->key, 0, $iv);
    }

    public function hash(string $texto): string
    {
        return hash('sha256', $texto . $this->key);
    }
}

