<?php

namespace OrionERP\Services;

class EmailTemplateService
{
    public function renderTemplate(string $template, array $variables = []): string
    {
        $templatePath = __DIR__ . '/../../resources/email_templates/' . $template . '.php';
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template no encontrado: $template");
        }

        extract($variables);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    public function getTemplateFactura(array $factura): string
    {
        return $this->renderTemplate('factura', [
            'numero' => $factura['numero_factura'],
            'fecha' => $factura['fecha_emision'],
            'cliente' => $factura['cliente_nombre'] ?? '',
            'total' => $factura['total']
        ]);
    }

    public function getTemplateNotificacion(string $titulo, string $mensaje): string
    {
        return $this->renderTemplate('notificacion', [
            'titulo' => $titulo,
            'mensaje' => $mensaje
        ]);
    }
}

