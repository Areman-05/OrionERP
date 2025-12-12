# 🏢 OrionERP - Sistema ERP para PYME

Sistema ERP completo y modular para pequeñas y medianas empresas desarrollado en PHP 8+ y MySQL.

## 🎯 Características Principales

### Autenticación y Seguridad
- ✅ Autenticación JWT para API REST
- ✅ Gestión de roles y permisos por módulo
- ✅ Middleware de autenticación y autorización
- ✅ Encriptación de datos sensibles
- ✅ Validación y sanitización de entrada
- ✅ Rate limiting y protección contra ataques
- ✅ Headers de seguridad
- ✅ Whitelist de IPs
- ✅ Gestión de sesiones

### Gestión de Productos
- ✅ CRUD completo de productos
- ✅ Variantes de productos (talla, color, etc.)
- ✅ Atributos personalizados de productos
- ✅ Categorías con estructura jerárquica
- ✅ Etiquetas y clasificaciones
- ✅ Histórico de cambios de productos
- ✅ Control de stock con alertas automáticas
- ✅ Stock mínimo y máximo
- ✅ Rotación de productos
- ✅ Productos más vendidos
- ✅ Búsqueda avanzada de productos
- ✅ Actualización masiva de precios
- ✅ Gestión de imágenes de productos

### Inventario
- ✅ Control de stock en tiempo real
- ✅ Movimientos de stock (entradas/salidas)
- ✅ Ajustes de inventario
- ✅ Transferencias entre almacenes
- ✅ Conteo físico de inventario
- ✅ Valoración de inventario
- ✅ Productos sin stock
- ✅ Productos con stock bajo
- ✅ Alertas automáticas de stock
- ✅ Rotación de productos por categoría

### Ventas y Compras
- ✅ Pedidos de venta y compra
- ✅ Líneas de pedido detalladas
- ✅ Estados de pedidos (pendiente, completado, cancelado)
- ✅ Seguimiento de entregas
- ✅ Recepción de pedidos con actualización automática de stock
- ✅ Histórico de pedidos por cliente/proveedor
- ✅ Descuentos y promociones
- ✅ Cálculo automático de impuestos

### Facturación
- ✅ Generación de facturas desde pedidos
- ✅ Líneas de factura detalladas
- ✅ Estados de factura (pendiente, pagada, vencida, cancelada)
- ✅ Generación de PDF de facturas
- ✅ Notas de crédito
- ✅ Aplicación de notas de crédito
- ✅ Facturas por cliente
- ✅ Facturas por estado
- ✅ Resumen de facturación
- ✅ Envío de facturas por email

### Clientes y Proveedores
- ✅ Gestión completa de clientes y proveedores
- ✅ Documentos asociados
- ✅ Estados y clasificaciones
- ✅ Histórico de compras/ventas
- ✅ Segmentación de clientes (VIP, Premium, Regular, Nuevo)
- ✅ Clientes morosos
- ✅ Búsqueda avanzada
- ✅ Estadísticas por cliente/proveedor
- ✅ Gestión de contactos
- ✅ Proveedores por volumen de compras

### Estadísticas e Informes
- ✅ Dashboard con KPIs en tiempo real
- ✅ Gráficas de ventas por mes, día, semana
- ✅ Productos más vendidos
- ✅ Ventas por vendedor
- ✅ Ventas por cliente
- ✅ Ventas por categoría
- ✅ Cálculo de beneficios y márgenes
- ✅ Comparación de períodos
- ✅ Tendencias de ventas
- ✅ Reportes de ventas, compras e inventario
- ✅ Reportes de margen de beneficio
- ✅ Exportación a PDF y Excel
- ✅ Exportación CSV

### Sistema y Configuración
- ✅ Configuración de empresa
- ✅ Sistema de backup automático
- ✅ Cache de consultas frecuentes
- ✅ Logs y auditoría completa
- ✅ Sistema de notificaciones
- ✅ Plantillas de email
- ✅ Cola de tareas
- ✅ Sistema de eventos
- ✅ Webhooks para integraciones
- ✅ API REST documentada
- ✅ Versionado de API
- ✅ Query builder avanzado
- ✅ Serialización de datos

### Utilidades
- ✅ Helpers para fechas (DateHelper)
- ✅ Helpers para números y monedas (NumberHelper)
- ✅ Helpers para arrays (ArrayHelper)
- ✅ Helpers para strings (StringHelper)
- ✅ Helpers para archivos (FileHelper)
- ✅ Helpers para JSON (JsonHelper)
- ✅ Validación de datos (Validator)
- ✅ Formateo de respuestas (ResponseHelper)
- ✅ Calculadora de operaciones

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

5. Configurar el servidor web
- Apache: Configurar DocumentRoot apuntando a `/public`
- Nginx: Configurar root apuntando a `/public`

6. Configurar permisos
```bash
chmod -R 755 storage
chmod -R 755 cache
chmod -R 755 backups
chmod -R 755 public/uploads
```

## 📁 Estructura del Proyecto

```
OrionERP/
├── app/
│   ├── Controllers/          # Controladores HTTP
│   │   ├── AuthController.php
│   │   ├── UsuarioController.php
│   │   ├── ProductoController.php
│   │   ├── ClienteController.php
│   │   ├── ProveedorController.php
│   │   ├── CompraController.php
│   │   ├── DashboardController.php
│   │   ├── EstadisticasController.php
│   │   ├── InformeController.php
│   │   ├── ExportacionController.php
│   │   ├── BackupController.php
│   │   ├── RotacionController.php
│   │   └── ConfiguracionController.php
│   │
│   ├── Models/               # Modelos de datos
│   │   ├── Usuario.php
│   │   ├── Producto.php
│   │   ├── VarianteProducto.php
│   │   ├── AtributoProducto.php
│   │   ├── Categoria.php
│   │   ├── Etiqueta.php
│   │   ├── Cliente.php
│   │   ├── Proveedor.php
│   │   ├── DocumentoCliente.php
│   │   ├── PedidoVenta.php
│   │   ├── PedidoCompra.php
│   │   ├── Factura.php
│   │   ├── LineaFactura.php
│   │   ├── SeguimientoEntrega.php
│   │   ├── HistoricoProducto.php
│   │   └── ConfiguracionEmpresa.php
│   │
│   ├── Services/             # Servicios de negocio (60+ servicios)
│   │   ├── ProductoService.php
│   │   ├── InventarioService.php
│   │   ├── StockService.php
│   │   ├── StockAlertaService.php
│   │   ├── MovimientoStockService.php
│   │   ├── ClienteService.php
│   │   ├── ProveedorService.php
│   │   ├── ContactoService.php
│   │   ├── PedidoService.php
│   │   ├── CompraService.php
│   │   ├── FacturacionService.php
│   │   ├── FacturaService.php
│   │   ├── NotaCreditoService.php
│   │   ├── EstadisticasService.php
│   │   ├── ReporteService.php
│   │   ├── ReporteVentasService.php
│   │   ├── ReporteComprasService.php
│   │   ├── ReportePdfService.php
│   │   ├── GraficoService.php
│   │   ├── DashboardService.php
│   │   ├── CategoriaService.php
│   │   ├── RotacionService.php
│   │   ├── UsuarioService.php
│   │   ├── RolService.php
│   │   ├── PermisoService.php
│   │   ├── SeguridadService.php
│   │   ├── AuthService.php
│   │   ├── TokenService.php
│   │   ├── PasswordService.php
│   │   ├── SesionService.php
│   │   ├── EncriptacionService.php
│   │   ├── ValidacionService.php
│   │   ├── ConfigService.php
│   │   ├── ConfiguracionService.php
│   │   ├── EmailService.php
│   │   ├── EmailTemplateService.php
│   │   ├── NotificacionService.php
│   │   ├── PdfService.php
│   │   ├── ExportacionService.php
│   │   ├── ExportacionExcelService.php
│   │   ├── ImportacionService.php
│   │   ├── BackupService.php
│   │   ├── CacheService.php
│   │   ├── LoggerService.php
│   │   ├── LogService.php
│   │   ├── AuditoriaService.php
│   │   ├── BuscadorService.php
│   │   ├── DescuentoService.php
│   │   ├── FormatoService.php
│   │   ├── CalculadoraService.php
│   │   ├── FileUploadService.php
│   │   ├── ImagenService.php
│   │   ├── ApiService.php
│   │   ├── ApiDocumentationService.php
│   │   ├── WebhookService.php
│   │   ├── IntegracionService.php
│   │   ├── QueryBuilderService.php
│   │   ├── SerializacionService.php
│   │   ├── ErrorHandlerService.php
│   │   ├── InformeService.php
│   │   ├── TareaProgramadaService.php
│   │   ├── QueueService.php
│   │   └── EventService.php
│   │
│   ├── Middleware/           # Middleware (16 middlewares)
│   │   ├── AuthMiddleware.php
│   │   ├── ApiAuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   ├── PermisoMiddleware.php
│   │   ├── CorsMiddleware.php
│   │   ├── RateLimitMiddleware.php
│   │   ├── SecurityHeadersMiddleware.php
│   │   ├── InputSanitizationMiddleware.php
│   │   ├── RequestValidationMiddleware.php
│   │   ├── RequestLoggingMiddleware.php
│   │   ├── ErrorHandlerMiddleware.php
│   │   ├── JsonMiddleware.php
│   │   ├── ApiResponseMiddleware.php
│   │   ├── ApiVersionMiddleware.php
│   │   ├── ConfigMiddleware.php
│   │   └── IpWhitelistMiddleware.php
│   │
│   ├── Utils/                # Utilidades y helpers (9 helpers)
│   │   ├── DateHelper.php
│   │   ├── NumberHelper.php
│   │   ├── StringHelper.php
│   │   ├── ArrayHelper.php
│   │   ├── FileHelper.php
│   │   ├── JsonHelper.php
│   │   ├── Validator.php
│   │   ├── ResponseHelper.php
│   │   └── ResponseFormatter.php
│   │
│   └── Core/                 # Núcleo de la aplicación
│       ├── Application.php
│       ├── Database.php
│       └── Router.php
│
├── public/                   # Punto de entrada público
│   ├── index.php
│   ├── css/                  # Estilos CSS
│   ├── js/                   # JavaScript
│   └── uploads/              # Archivos subidos
│
├── database/
│   └── schema.sql            # Esquema de base de datos
│
├── backups/                  # Backups de base de datos
├── cache/                    # Cache de consultas
├── storage/                  # Almacenamiento de archivos
├── vendor/                   # Dependencias de Composer
├── .env                      # Variables de entorno
├── .env.example              # Ejemplo de variables de entorno
├── composer.json             # Dependencias PHP
└── README.md                 # Este archivo
```

## 🔧 Módulos Implementados

### Gestión de Productos
- CRUD completo de productos con validaciones
- Variantes de productos (talla, color, etc.)
- Atributos personalizados
- Categorías con estructura jerárquica
- Etiquetas y clasificaciones múltiples
- Histórico completo de cambios
- Control de stock con alertas automáticas
- Búsqueda avanzada por múltiples criterios
- Actualización masiva de precios
- Gestión de imágenes

### Inventario
- Control de stock en tiempo real
- Movimientos de stock (entradas/salidas/ajustes)
- Transferencias entre almacenes
- Conteo físico con comparación automática
- Valoración de inventario
- Alertas de stock bajo y sin stock
- Rotación de productos
- Análisis por categoría

### Ventas y Compras
- Pedidos de venta y compra completos
- Líneas de pedido detalladas
- Estados y seguimiento de pedidos
- Recepción de pedidos con actualización automática
- Histórico completo por cliente/proveedor
- Descuentos y promociones
- Cálculo automático de impuestos

### Facturación
- Generación automática de facturas desde pedidos
- Líneas de factura detalladas
- Estados de factura (pendiente, pagada, vencida, cancelada)
- Generación de PDF profesional
- Notas de crédito con aplicación automática
- Resúmenes y reportes de facturación
- Envío automático por email

### Clientes y Proveedores
- Gestión completa con documentos
- Segmentación automática de clientes
- Identificación de clientes morosos
- Histórico de compras/ventas
- Estadísticas por cliente/proveedor
- Búsqueda avanzada
- Gestión de contactos múltiples
- Análisis de proveedores por volumen

### Estadísticas e Informes
- Dashboard con KPIs en tiempo real
- Gráficas de ventas (día, semana, mes)
- Análisis por vendedor, cliente, categoría
- Cálculo de beneficios y márgenes
- Comparación de períodos
- Tendencias y proyecciones
- Reportes completos (ventas, compras, inventario)
- Exportación a PDF, Excel y CSV

### Sistema y Configuración
- Autenticación JWT para API
- Roles y permisos granulares
- Configuración de empresa
- Sistema de backup automático
- Cache inteligente de consultas
- Logs y auditoría completa
- Sistema de notificaciones
- Cola de tareas asíncronas
- Sistema de eventos
- Webhooks para integraciones
- API REST documentada
- Versionado de API

## 🔐 Seguridad

- Autenticación JWT con tokens seguros
- Encriptación de datos sensibles
- Validación y sanitización de entrada
- Rate limiting para prevenir abusos
- Headers de seguridad HTTP
- Whitelist de IPs configurable
- Logs de auditoría de todas las acciones
- Protección CSRF
- Sanitización de salida

## 📊 API REST

El sistema incluye una API REST completa con:
- Autenticación JWT
- Versionado de API
- Documentación automática
- Validación de entrada
- Respuestas estandarizadas
- Manejo de errores consistente
- Rate limiting
- CORS configurable

## 🔐 Usuario por defecto

- **Email:** admin@orionerp.com
- **Password:** admin123

⚠️ **IMPORTANTE:** Cambiar la contraseña inmediatamente en producción.

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 8.0+
- **Base de datos:** MySQL 5.7+ / MariaDB 10.3+
- **Framework:** Slim Framework (PSR-7, PSR-11, PSR-15)
- **Autenticación:** JWT (JSON Web Tokens)
- **PDF:** TCPDF / DomPDF
- **Email:** PHPMailer
- **Cache:** Sistema de cache basado en archivos
- **Logging:** Monolog
- **Validación:** Validator personalizado

## 📝 Licencia

Proyecto privado - Todos los derechos reservados

## 👥 Contribuciones

Este es un proyecto privado. Para contribuciones, contactar con el equipo de desarrollo.

## 📞 Soporte

Para soporte técnico o consultas, contactar con el equipo de desarrollo.

---

**OrionERP** - Sistema ERP completo para PYME desarrollado con PHP moderno y mejores prácticas de desarrollo.
