# 🏢 OrionERP - Sistema ERP para PYME

Sistema ERP completo y modular para pequeñas y medianas empresas desarrollado en PHP 8+ y MySQL.

## 🎯 Características Principales

- ✅ Autenticación y gestión de roles con permisos por módulo
- ✅ Dashboard con KPIs y estadísticas avanzadas
- ✅ Gestión completa de productos con variantes (talla, color)
- ✅ Control de stock e inventario con alertas automáticas
- ✅ Gestión de clientes y proveedores con documentos
- ✅ Módulo de ventas y compras completo
- ✅ Facturación con generación de PDF y líneas detalladas
- ✅ Sistema de notificaciones mejorado con alertas automáticas
- ✅ Logs y auditoría completa
- ✅ API REST interna con autenticación JWT
- ✅ Buscador avanzado de productos y clientes
- ✅ Exportación CSV de datos
- ✅ Sistema de backup de base de datos
- ✅ Histórico de cambios de productos
- ✅ Rotación de productos
- ✅ Seguimiento de entregas de proveedores
- ✅ Sistema de configuración de empresa
- ✅ Informes avanzados (ventas, gastos, stock)
- ✅ Categorías de productos con estructura de árbol
- ✅ Validaciones mejoradas en formularios
- ✅ Sistema de cache para consultas frecuentes

## 🚀 Requisitos

- PHP >= 8.0
- MySQL >= 5.7 o MariaDB >= 10.3
- Composer
- Extensiones PHP: pdo_mysql, mbstring, gd, zip

## 📦 Instalación

1. Clonar el repositorio
2. Instalar dependencias: `composer install`
3. Configurar `.env` con los datos de conexión a la base de datos
4. Importar el esquema de base de datos desde `/database/schema.sql`
5. Configurar el servidor web para apuntar a `/public`

## 📁 Estructura del Proyecto

```
/app
    /Controllers      - Controladores de la aplicación
    /Models          - Modelos de datos
    /Services        - Servicios de negocio
    /Middleware      - Middleware de autenticación y permisos
    /Core            - Núcleo de la aplicación
    /Utils           - Utilidades y helpers
/public
    /css            - Estilos CSS
    /js             - JavaScript
    /uploads        - Archivos subidos
/database
    schema.sql      - Esquema de base de datos
/backups           - Backups de base de datos
/cache             - Cache de consultas
/vendor            - Dependencias de Composer
```

## 🔧 Módulos Implementados

### Gestión de Productos
- CRUD completo de productos
- Variantes de productos (talla, color, etc.)
- Categorías con estructura jerárquica
- Histórico de cambios
- Control de stock con alertas

### Ventas y Compras
- Pedidos de venta y compra
- Líneas de pedido detalladas
- Facturación con PDF
- Seguimiento de entregas
- Recepción de pedidos con actualización automática de stock

### Clientes y Proveedores
- Gestión completa de clientes y proveedores
- Documentos asociados
- Estados y clasificaciones
- Histórico de pedidos

### Estadísticas e Informes
- Dashboard con KPIs
- Gráficas de ventas por mes
- Productos más vendidos
- Cálculo de beneficios
- Informes de ventas, gastos y stock
- Rotación de productos

### Sistema y Configuración
- Autenticación JWT para API
- Permisos por módulo
- Configuración de empresa
- Sistema de backup
- Cache de consultas
- Logs y auditoría

## 🔐 Usuario por defecto

- Email: admin@orionerp.com
- Password: admin123 (cambiar en producción)

## 📝 Licencia

Proyecto privado - Todos los derechos reservados

