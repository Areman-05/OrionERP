<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use OrionERP\Services\CacheService;

class RateLimitMiddleware implements MiddlewareInterface
{
    private $cacheService;
    private $maxRequests;
    private $windowSeconds;

    public function __construct(int $maxRequests = 100, int $windowSeconds = 60)
    {
        $this->cacheService = new CacheService();
        $this->maxRequests = $maxRequests;
        $this->windowSeconds = $windowSeconds;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ipAddress = $this->getClientIp($request);
        $key = 'rate_limit_' . md5($ipAddress);
        
        $requests = $this->cacheService->get($key) ?? 0;
        
        if ($requests >= $this->maxRequests) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Demasiadas solicitudes. Por favor, intente más tarde.',
                'retry_after' => $this->windowSeconds
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('X-RateLimit-Limit', (string) $this->maxRequests)
                ->withHeader('X-RateLimit-Remaining', '0')
                ->withHeader('Retry-After', (string) $this->windowSeconds)
                ->withStatus(429);
        }
        
        $this->cacheService->set($key, $requests + 1, $this->windowSeconds);
        
        $response = $handler->handle($request);
        
        return $response
            ->withHeader('X-RateLimit-Limit', (string) $this->maxRequests)
            ->withHeader('X-RateLimit-Remaining', (string) max(0, $this->maxRequests - $requests - 1));
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

