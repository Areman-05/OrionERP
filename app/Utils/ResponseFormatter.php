<?php

namespace OrionERP\Utils;

use Psr\Http\Message\ResponseInterface;

class ResponseFormatter
{
    public static function success(ResponseInterface $response, $data = null, string $message = 'Operación exitosa', int $statusCode = 200): ResponseInterface
    {
        $body = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $body['data'] = $data;
        }

        $response->getBody()->write(json_encode($body));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    public static function error(ResponseInterface $response, string $message = 'Error en la operación', int $statusCode = 400, array $errors = []): ResponseInterface
    {
        $body = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors)) {
            $body['errors'] = $errors;
        }

        $response->getBody()->write(json_encode($body));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    public static function paginated(ResponseInterface $response, array $data, int $total, int $page = 1, int $perPage = 20): ResponseInterface
    {
        $body = [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ]
        ];

        $response->getBody()->write(json_encode($body));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}

