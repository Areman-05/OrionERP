# 🏢 OrionERP - Sistema ERP para PYME

Sistema ERP completo y modular para pequeñas y medianas empresas desarrollado en PHP 8+ y MySQL.

## 🎯 Características Principales

- ✅ Autenticación y gestión de roles
- ✅ Dashboard con KPIs y estadísticas
- ✅ Gestión completa de productos
- ✅ Control de stock e inventario
- ✅ Gestión de clientes y proveedores
- ✅ Módulo de ventas y compras
- ✅ Facturación con generación de PDF
- ✅ Sistema de notificaciones
- ✅ Logs y auditoría
- ✅ API REST interna

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
    /controllers
    /models
    /views
    /services
    /middleware
/public
    /css
    /js
    /uploads
/config
/database
/resources
/routes
/vendor
```

## 🔐 Usuario por defecto

- Email: admin@orionerp.com
- Password: admin123 (cambiar en producción)

## 📝 Licencia

Proyecto privado - Todos los derechos reservados

