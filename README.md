# 🏢 OrionERP - Sistema ERP para PYME

Sistema ERP completo y modular para pequeñas y medianas empresas desarrollado en PHP 8+ y MySQL.

## 🎯 Características Principales

- ✅ **Autenticación y Seguridad:** JWT, roles, permisos, validación, rate limiting
- ✅ **Gestión de Productos:** CRUD, variantes, categorías, control de stock, alertas
- ✅ **Inventario:** Control en tiempo real, movimientos, ajustes, rotación
- ✅ **Ventas y Compras:** Pedidos, seguimiento, recepción automática
- ✅ **Facturación:** Generación de facturas, PDF, notas de crédito
- ✅ **Clientes y Proveedores:** Gestión completa, segmentación, estadísticas
- ✅ **Estadísticas e Informes:** Dashboard con KPIs, gráficas, reportes, exportación
- ✅ **Sistema:** Backup automático, cache, logs, notificaciones, API REST

## 🚀 Requisitos

- PHP >= 8.0
- MySQL >= 5.7 o MariaDB >= 10.3
- Composer
- Extensiones PHP: pdo_mysql, mbstring, gd, zip, json

## 📦 Instalación

1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/OrionERP.git
cd OrionERP
```

2. Instalar dependencias
```bash
composer install
```

3. Configurar variables de entorno
```bash
cp .env.example .env
# Editar .env con los datos de conexión a la base de datos
```

4. Importar el esquema de base de datos
```bash
mysql -u usuario -p nombre_base_datos < database/schema.sql
```

5. Configurar el servidor web (Apache/Nginx) apuntando a `/public`

6. Configurar permisos
```bash
chmod -R 755 storage cache backups public/uploads
```

## 📁 Estructura del Proyecto

```
OrionERP/
├── app/
│   ├── Controllers/      # Controladores HTTP (12 controladores)
│   ├── Models/           # Modelos de datos (16 modelos)
│   ├── Services/         # Servicios de negocio (60+ servicios)
│   ├── Middleware/       # Middleware (16 middlewares)
│   ├── Utils/            # Utilidades y helpers (9 helpers)
│   └── Core/             # Núcleo de la aplicación
├── public/               # Punto de entrada público
├── database/             # Esquema de base de datos
├── backups/              # Backups automáticos
├── cache/                # Cache de consultas
└── storage/              # Almacenamiento de archivos
```

## 🔧 Módulos Principales

- **Productos:** CRUD, variantes, categorías, stock, alertas, búsqueda
- **Inventario:** Control en tiempo real, movimientos, ajustes, rotación
- **Ventas/Compras:** Pedidos, seguimiento, recepción automática
- **Facturación:** Facturas, PDF, notas de crédito, envío por email
- **Clientes/Proveedores:** Gestión, segmentación, estadísticas, contactos
- **Estadísticas:** Dashboard, KPIs, gráficas, reportes, exportación
- **Sistema:** Autenticación JWT, roles, backup, cache, logs, API REST

## 🔐 Seguridad

- Autenticación JWT
- Roles y permisos granulares
- Validación y sanitización
- Rate limiting
- Headers de seguridad
- Logs de auditoría

## 📊 API REST

API REST completa con autenticación JWT, versionado, documentación automática, validación y rate limiting.

## 🔐 Usuario por defecto

- **Email:** admin@orionerp.com
- **Password:** admin123

⚠️ **IMPORTANTE:** Cambiar la contraseña en producción.

## 🛠️ Tecnologías

- **Backend:** PHP 8.0+
- **Base de datos:** MySQL 5.7+ / MariaDB 10.3+
- **Framework:** Slim Framework (PSR-7, PSR-11, PSR-15)
- **Autenticación:** JWT
- **PDF:** TCPDF / DomPDF
- **Email:** PHPMailer
- **Cache:** Sistema de cache basado en archivos
- **Logging:** Monolog

## 📝 Licencia

Proyecto privado - Todos los derechos reservados

---

**OrionERP** - Sistema ERP completo para PYME desarrollado con PHP moderno y mejores prácticas.
