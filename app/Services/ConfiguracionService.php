<?php

namespace OrionERP\Services;

use OrionERP\Models\ConfiguracionEmpresa;

class ConfiguracionService
{
    private $configModel;

    public function __construct()
    {
        $this->configModel = new ConfiguracionEmpresa();
    }

    public function getConfiguracionCompleta(): array
    {
        return $this->configModel->getAll();
    }

    public function getValor(string $clave, $default = null)
    {
        return $this->configModel->get($clave, $default);
    }

    public function setValor(string $clave, $valor, string $tipo = 'texto'): bool
    {
        return $this->configModel->set($clave, $valor, $tipo);
    }

    public function getConfiguracionFacturacion(): array
    {
        return [
            'iva_por_defecto' => (float) $this->getValor('iva_por_defecto', 21),
            'moneda' => $this->getValor('moneda', 'EUR'),
            'dias_vencimiento' => (int) $this->getValor('dias_vencimiento_factura', 30)
        ];
    }

    public function getDatosEmpresa(): array
    {
        return [
            'nombre' => $this->getValor('nombre_empresa', 'OrionERP'),
            'cif' => $this->getValor('cif', ''),
            'direccion' => $this->getValor('direccion', ''),
            'telefono' => $this->getValor('telefono', ''),
            'email' => $this->getValor('email', '')
        ];
    }
}


