<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use OrionERP\Services\ConfiguracionService;

class ConfigMiddleware implements MiddlewareInterface
{
    private $configService;

    public function __construct()
    {
        $this->configService = new ConfiguracionService();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Agregar configuración a los atributos de la request
        $request = $request->withAttribute('config', $this->configService->getConfiguracionCompleta());
        $request = $request->withAttribute('empresa', $this->configService->getDatosEmpresa());
        
        return $handler->handle($request);
    }
}


