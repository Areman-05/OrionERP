<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        // X-Content-Type-Options
        $response = $response->withHeader('X-Content-Type-Options', 'nosniff');
        
        // X-Frame-Options
        $response = $response->withHeader('X-Frame-Options', 'DENY');
        
        // X-XSS-Protection
        $response = $response->withHeader('X-XSS-Protection', '1; mode=block');
        
        // Referrer-Policy
        $response = $response->withHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content-Security-Policy
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';";
        $response = $response->withHeader('Content-Security-Policy', $csp);

        return $response;
    }
}
