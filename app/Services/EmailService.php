<?php

namespace OrionERP\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;
    private $config;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->config = $this->getConfig();
        $this->configure();
    }

    private function configure(): void
    {
        try {
            // Configuración del servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp_host'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp_user'] ?? '';
            $this->mailer->Password = $this->config['smtp_password'] ?? '';
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->config['smtp_port'] ?? 587;
            $this->mailer->CharSet = 'UTF-8';

            // Remitente
            $this->mailer->setFrom(
                $this->config['from_email'] ?? 'noreply@orionerp.com',
                $this->config['from_name'] ?? 'OrionERP'
            );
        } catch (Exception $e) {
            error_log("Error configurando EmailService: " . $e->getMessage());
        }
    }

    public function enviar(string $destinatario, string $asunto, string $mensaje, bool $esHTML = true): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinatario);
            $this->mailer->isHTML($esHTML);
            $this->mailer->Subject = $asunto;
            $this->mailer->Body = $mensaje;
            $this->mailer->AltBody = strip_tags($mensaje);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Error enviando email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function enviarNotificacionStockBajo(string $destinatario, array $productos): bool
    {
        $mensaje = "<h2>Alertas de Stock Bajo</h2>";
        $mensaje .= "<p>Los siguientes productos tienen stock bajo:</p><ul>";
        
        foreach ($productos as $producto) {
            $mensaje .= "<li>{$producto['nombre']} - Stock actual: {$producto['stock_actual']} (Mínimo: {$producto['stock_minimo']})</li>";
        }
        
        $mensaje .= "</ul>";

        return $this->enviar($destinatario, 'Alertas de Stock Bajo - OrionERP', $mensaje);
    }

    public function enviarFactura(string $destinatario, array $factura, string $archivoPDF = null): bool
    {
        $mensaje = "<h2>Nueva Factura</h2>";
        $mensaje .= "<p>Se ha generado una nueva factura:</p>";
        $mensaje .= "<p><strong>Número:</strong> {$factura['numero_factura']}</p>";
        $mensaje .= "<p><strong>Fecha:</strong> {$factura['fecha_emision']}</p>";
        $mensaje .= "<p><strong>Total:</strong> " . number_format($factura['total'], 2) . " €</p>";

        if ($archivoPDF && file_exists($archivoPDF)) {
            $this->mailer->addAttachment($archivoPDF, 'factura.pdf');
        }

        return $this->enviar($destinatario, "Factura {$factura['numero_factura']} - OrionERP", $mensaje);
    }

    private function getConfig(): array
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        return [
            'smtp_host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
            'smtp_user' => $_ENV['SMTP_USER'] ?? '',
            'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'smtp_port' => $_ENV['SMTP_PORT'] ?? 587,
            'from_email' => $_ENV['EMAIL_FROM'] ?? 'noreply@orionerp.com',
            'from_name' => $_ENV['EMAIL_FROM_NAME'] ?? 'OrionERP'
        ];
    }
}

