<?php

namespace OrionERP\Services;

use OrionERP\Core\Database;

class WebhookService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function enviarWebhook(string $evento, array $datos, string $url): bool
    {
        $payload = [
            'evento' => $evento,
            'datos' => $datos,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Webhook-Signature: ' . $this->generarFirma($payload)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Registrar webhook
        $this->db->query(
            "INSERT INTO logs (accion, tabla, datos_nuevos) 
             VALUES (?, ?, ?)",
            ['webhook_enviado', 'webhooks', json_encode(['evento' => $evento, 'url' => $url, 'codigo' => $httpCode])]
        );

        return $httpCode >= 200 && $httpCode < 300;
    }

    private function generarFirma(array $payload): string
    {
        $secret = $_ENV['WEBHOOK_SECRET'] ?? 'default_secret';
        return hash_hmac('sha256', json_encode($payload), $secret);
    }
}

