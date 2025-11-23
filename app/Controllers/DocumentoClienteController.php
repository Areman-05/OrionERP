<?php

namespace OrionERP\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OrionERP\Models\DocumentoCliente;

class DocumentoClienteController
{
    private $documentoModel;

    public function __construct()
    {
        $this->documentoModel = new DocumentoCliente();
    }

    public function getByCliente(Request $request, Response $response, array $args): Response
    {
        $clienteId = (int) $args['cliente_id'];
        $documentos = $this->documentoModel->getByCliente($clienteId);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $documentos
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        if (empty($data['cliente_id']) || empty($data['nombre']) || empty($data['archivo'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Cliente, nombre y archivo son requeridos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $id = $this->documentoModel->create($data);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Documento agregado exitosamente',
            'id' => $id
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        
        $this->documentoModel->delete($id);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Documento eliminado exitosamente'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}

