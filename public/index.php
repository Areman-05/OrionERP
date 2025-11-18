<?php
/**
 * OrionERP - Punto de entrada principal
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use OrionERP\Core\Application;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Inicializar la aplicación
$app = new Application();
$app->run();

