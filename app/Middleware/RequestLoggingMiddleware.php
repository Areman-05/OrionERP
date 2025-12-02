<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use OrionERP\Services\LoggerService;

class RequestLoggingMiddleware implements MiddlewareInterface
{
    private $logger;

    public function __construct()
    {
        $this->logger = new LoggerService();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startTime = microtime(true);
        
        $response = $handler->handle($request);
        
        $duration = microtime(true) - $startTime;
        $method = $request->getMethod();
        $uri = $request->getUri()->getPath();
        $statusCode = $response->getStatusCode();
        
        $this->logger->info("Request: $method $uri - Status: $statusCode - Duration: " . round($duration * 1000, 2) . "ms");
        
        return $response;
    }
}

