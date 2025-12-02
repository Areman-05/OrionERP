<?php

namespace OrionERP\Core;

use Slim\Factory\AppFactory;
use Slim\App;

class Router
{
    private $app;

    public function __construct()
    {
        $this->app = AppFactory::create();
    }

    public function setupRoutes(): App
    {
        // Rutas de autenticación
        $this->app->post('/api/auth/login', \OrionERP\Controllers\ApiController::class . ':login');
        $this->app->get('/api/auth/verify', \OrionERP\Controllers\ApiController::class . ':verificarToken')
            ->add(\OrionERP\Middleware\ApiAuthMiddleware::class);

        // Rutas de productos
        $this->app->get('/api/productos', \OrionERP\Controllers\ProductoController::class . ':index');
        $this->app->get('/api/productos/{id}', \OrionERP\Controllers\ProductoController::class . ':show');
        $this->app->post('/api/productos', \OrionERP\Controllers\ProductoController::class . ':store');
        $this->app->put('/api/productos/{id}', \OrionERP\Controllers\ProductoController::class . ':update');
        $this->app->delete('/api/productos/{id}', \OrionERP\Controllers\ProductoController::class . ':destroy');

        // Rutas de clientes
        $this->app->get('/api/clientes', \OrionERP\Controllers\ClienteController::class . ':index');
        $this->app->get('/api/clientes/{id}', \OrionERP\Controllers\ClienteController::class . ':show');
        $this->app->post('/api/clientes', \OrionERP\Controllers\ClienteController::class . ':store');
        $this->app->put('/api/clientes/{id}', \OrionERP\Controllers\ClienteController::class . ':update');

        // Rutas de dashboard
        $this->app->get('/api/dashboard', \OrionERP\Controllers\DashboardController::class . ':index');

        return $this->app;
    }

    public function getApp(): App
    {
        return $this->app;
    }
}

