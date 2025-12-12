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

    public static function paginated(Response $response, array $data, int $total, int $page, int $perPage, string $message = 'Datos obtenidos exitosamente'): Response
    {
        $body = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ]
        ];

        $response->getBody()->write(json_encode($body));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function validationError(Response $response, array $errors, string $message = 'Error de validacion'): Response
    {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
    }

    public static function notFound(Response $response, string $message = 'Recurso no encontrado'): Response
    {
        return self::error($response, $message, 404);
    }

    public static function unauthorized(Response $response, string $message = 'No autorizado'): Response
    {
        return self::error($response, $message, 401);
    }
}

