<?php

namespace OrionERP\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use OrionERP\Utils\Validator;

class RequestValidationMiddleware implements MiddlewareInterface
{
    private $rules;

    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (empty($this->rules)) {
            return $handler->handle($request);
        }

        $data = $request->getParsedBody() ?? [];
        $validator = new Validator();
        $errors = [];

        foreach ($this->rules as $field => $rule) {
            $rulesArray = explode('|', $rule);
            
            foreach ($rulesArray as $singleRule) {
                if (strpos($singleRule, ':') !== false) {
                    [$ruleName, $ruleValue] = explode(':', $singleRule, 2);
                } else {
                    $ruleName = $singleRule;
                    $ruleValue = null;
                }

                $value = $data[$field] ?? null;

                if ($ruleName === 'required' && empty($value)) {
                    $errors[$field][] = "El campo $field es requerido";
                } elseif ($ruleName === 'email' && !empty($value) && !$validator->email($value)) {
                    $errors[$field][] = "El campo $field debe ser un email válido";
                } elseif ($ruleName === 'numeric' && !empty($value) && !is_numeric($value)) {
                    $errors[$field][] = "El campo $field debe ser numérico";
                } elseif ($ruleName === 'min' && !empty($value) && strlen($value) < (int)$ruleValue) {
                    $errors[$field][] = "El campo $field debe tener al menos $ruleValue caracteres";
                }
            }
        }

        if (!empty($errors)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $errors
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

        return $handler->handle($request);
    }
}

