<?php

namespace OrionERP\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

class LoggerService
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('OrionERP');
        
        // Handler para archivo con rotación diaria
        $logDir = __DIR__ . '/../../logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $this->logger->pushHandler(new RotatingFileHandler($logDir . 'app.log', 30, Logger::DEBUG));
        
        // Handler para errores
        $this->logger->pushHandler(new StreamHandler($logDir . 'error.log', Logger::ERROR));
    }

    public function info(string $mensaje, array $context = []): void
    {
        $this->logger->info($mensaje, $context);
    }

    public function warning(string $mensaje, array $context = []): void
    {
        $this->logger->warning($mensaje, $context);
    }

    public function error(string $mensaje, array $context = []): void
    {
        $this->logger->error($mensaje, $context);
    }

    public function debug(string $mensaje, array $context = []): void
    {
        $this->logger->debug($mensaje, $context);
    }
}

