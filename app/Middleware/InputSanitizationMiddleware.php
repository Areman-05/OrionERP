<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use OrionERP\Utils\Validator;

class InputSanitizationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Sanitizar body
        $body = $request->getParsedBody();
        if (is_array($body)) {
            $body = Validator::sanitizeArray($body);
            $request = $request->withParsedBody($body);
        }

        // Sanitizar query params
        $queryParams = $request->getQueryParams();
        if (!empty($queryParams)) {
            $queryParams = Validator::sanitizeArray($queryParams);
            $uri = $request->getUri();
            $queryString = http_build_query($queryParams);
            $uri = $uri->withQuery($queryString);
            $request = $request->withUri($uri);
        }

        return $handler->handle($request);
    }
}

