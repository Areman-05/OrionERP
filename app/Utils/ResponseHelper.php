<?php

namespace OrionERP\Utils;

use Psr\Http\Message\ResponseInterface as Response;

class ResponseHelper
{
    public static function success(Response $response, $data = null, string $message = 'Operacion exitosa', int $statusCode = 200): Response
    {
        $body = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $body['data'] = $data;
        }

        $response->getBody()->write(json_encode($body));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    public static function error(Response $response, string $message = 'Error en la operacion', int $statusCode = 400): Response
    {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $message
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}

