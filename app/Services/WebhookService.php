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

    public function registrarWebhook(string $url, string $evento, array $configuracion = []): int
    {
        $this->db->query(
            "INSERT INTO webhooks (url, evento, configuracion, activo)
             VALUES (?, ?, ?, 1)",
            [$url, $evento, json_encode($configuracion)]
        );

        return (int) $this->db->lastInsertId();
    }

    public function ejecutarWebhook(string $evento, array $datos): void
    {
        $webhooks = $this->db->fetchAll(
            "SELECT * FROM webhooks WHERE evento = ? AND activo = 1",
            [$evento]
        );

        foreach ($webhooks as $webhook) {
            $this->enviarWebhook($webhook['url'], $evento, $datos, json_decode($webhook['configuracion'], true));
        }
    }

    private function enviarWebhook(string $url, string $evento, array $datos, array $configuracion): void
    {
        $payload = [
            'evento' => $evento,
            'timestamp' => date('Y-m-d H:i:s'),
            'datos' => $datos
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Webhook-Signature: ' . hash_hmac('sha256', json_encode($payload), $configuracion['secret'] ?? '')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        curl_exec($ch);
        curl_close($ch);
    }

    public function desactivarWebhook(int $webhookId): bool
    {
        return $this->db->query(
            "UPDATE webhooks SET activo = 0 WHERE id = ?",
            [$webhookId]
        );
    }

    public function getWebhooksPorEvento(string $evento): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM webhooks WHERE evento = ? AND activo = 1",
            [$evento]
        );
    }
}
