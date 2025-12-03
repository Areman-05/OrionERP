<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class IpWhitelistMiddleware implements MiddlewareInterface
{
    private $allowedIps;

    public function __construct(array $allowedIps = [])
    {
        $this->allowedIps = $allowedIps;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (empty($this->allowedIps)) {
            return $handler->handle($request);
        }

        $clientIp = $this->getClientIp($request);
        
        if (!in_array($clientIp, $this->allowedIps)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Acceso denegado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        return $handler->handle($request);
    }

    private function getClientIp(ServerRequestInterface $request): string
    {
        $serverParams = $request->getServerParams();
        
        if (isset($serverParams['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        return $serverParams['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

