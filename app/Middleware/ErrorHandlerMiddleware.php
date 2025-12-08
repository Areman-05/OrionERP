<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private $displayErrors;

    public function __construct(bool $displayErrors = false)
    {
        $this->displayErrors = $displayErrors;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (\Exception $e) {
            $statusCode = method_exists($e, 'getCode') && $e->getCode() >= 400 && $e->getCode() < 600 
                ? $e->getCode() 
                : 500;

            $errorData = [
                'success' => false,
                'message' => 'Ha ocurrido un error en el servidor',
                'error' => $this->displayErrors ? $e->getMessage() : null
            ];

            if ($this->displayErrors) {
                $errorData['trace'] = $e->getTraceAsString();
                $errorData['file'] = $e->getFile();
                $errorData['line'] = $e->getLine();
            }

            $response = new \Slim\Psr7\Response($statusCode);
            $response->getBody()->write(json_encode($errorData));
            
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}

