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

    public function actualizarConfiguracion(array $configuracion): bool
    {
        $exito = true;
        foreach ($configuracion as $clave => $valor) {
            if (!$this->setValor($clave, $valor)) {
                $exito = false;
            }
        }
        return $exito;
    }

    public function getConfiguracionStock(): array
    {
        return [
            'alerta_stock_bajo' => (bool) $this->getValor('alerta_stock_bajo', true),
            'stock_minimo_por_defecto' => (int) $this->getValor('stock_minimo_por_defecto', 10),
            'control_stock_automatico' => (bool) $this->getValor('control_stock_automatico', true)
        ];
    }

    public function getConfiguracionGeneral(): array
    {
        return [
            'timezone' => $this->getValor('timezone', 'Europe/Madrid'),
            'idioma' => $this->getValor('idioma', 'es'),
            'formato_fecha' => $this->getValor('formato_fecha', 'd/m/Y'),
            'formato_hora' => $this->getValor('formato_hora', 'H:i:s')
        ];
    }
}


