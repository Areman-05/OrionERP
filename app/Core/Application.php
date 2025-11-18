<?php

namespace OrionERP\Core;

use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application
{
    private $app;

    public function __construct()
    {
        $this->app = AppFactory::create();
        $this->setupMiddleware();
        $this->setupRoutes();
    }

    private function setupMiddleware(): void
    {
        // Middleware de errores
        $this->app->addErrorMiddleware(true, true, true);
        
        // Middleware de CORS (si es necesario)
        $this->app->add(function (ServerRequestInterface $request, $handler) {
            $response = $handler->handle($request);
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        });
    }

    private function setupRoutes(): void
    {
        $this->app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) {
            $response->getBody()->write('OrionERP - Sistema ERP para PYME');
            return $response;
        });
    }

    public function run(): void
    {
        $this->app->run();
    }

    public function getApp()
    {
        return $this->app;
    }
}

