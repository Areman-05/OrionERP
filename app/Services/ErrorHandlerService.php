<?php

namespace OrionERP\Services;

use OrionERP\Services\LoggerService;

class ErrorHandlerService
{
    private $logger;

    public function __construct()
    {
        $this->logger = new LoggerService();
    }

    public function registrarError(\Throwable $error, array $context = []): void
    {
        $this->logger->error($error->getMessage(), [
            'exception' => get_class($error),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
            'context' => $context
        ]);
    }

    public function formatearErrorParaUsuario(\Throwable $error, bool $mostrarDetalles = false): array
    {
        $mensaje = 'Ha ocurrido un error en el sistema';
        
        if ($mostrarDetalles || $_ENV['APP_DEBUG'] === 'true') {
            $mensaje = $error->getMessage();
        }
        
        return [
            'success' => false,
            'message' => $mensaje,
            'error_code' => $error->getCode()
        ];
    }
}

