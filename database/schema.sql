-- OrionERP - Esquema de Base de Datos
-- Versión inicial

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de datos
CREATE DATABASE IF NOT EXISTS `orionerp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `orionerp`;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','gerente','empleado') NOT NULL DEFAULT 'empleado',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_rol` (`rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de sesiones (auditoría)
CREATE TABLE IF NOT EXISTS `sesiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_token` (`token`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de categorías de productos
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `categoria_padre_id` int(11) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_padre` (`categoria_padre_id`),
  FOREIGN KEY (`categoria_padre_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL UNIQUE,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text,
  `categoria_id` int(11) DEFAULT NULL,
  `precio_venta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `tiene_variantes` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_activo` (`activo`),
  FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de atributos de productos (talla, color, etc)
CREATE TABLE IF NOT EXISTS `atributos_producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `tipo` enum('texto','numero','opcion') NOT NULL DEFAULT 'texto',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de valores de atributos
CREATE TABLE IF NOT EXISTS `valores_atributo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `atributo_id` int(11) NOT NULL,
  `valor` varchar(100) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_atributo` (`atributo_id`),
  FOREIGN KEY (`atributo_id`) REFERENCES `atributos_producto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de variantes de productos
CREATE TABLE IF NOT EXISTS `variantes_producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `precio_venta` decimal(10,2) DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_sku` (`sku`),
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de valores de variantes (relación variante-atributo-valor)
CREATE TABLE IF NOT EXISTS `variante_atributos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variante_id` int(11) NOT NULL,
  `atributo_id` int(11) NOT NULL,
  `valor_id` int(11) DEFAULT NULL,
  `valor_texto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_variante` (`variante_id`),
  KEY `idx_atributo` (`atributo_id`),
  KEY `idx_valor` (`valor_id`),
  FOREIGN KEY (`variante_id`) REFERENCES `variantes_producto` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`atributo_id`) REFERENCES `atributos_producto` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`valor_id`) REFERENCES `valores_atributo` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de movimientos de stock
CREATE TABLE IF NOT EXISTS `movimientos_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_usuario` (`usuario_id`),
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL UNIQUE,
  `nombre` varchar(200) NOT NULL,
  `tipo_documento` enum('DNI','CIF','NIE','Pasaporte') DEFAULT 'DNI',
  `numero_documento` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text,
  `ciudad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `pais` varchar(50) DEFAULT 'España',
  `estado` enum('activo','moroso','VIP','inactivo') NOT NULL DEFAULT 'activo',
  `notas` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de documentos de clientes
CREATE TABLE IF NOT EXISTS `documentos_cliente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `tipo` enum('DNI','CIF','Contrato','Factura','Otro') NOT NULL DEFAULT 'Otro',
  `nombre` varchar(255) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `descripcion` text,
  `fecha_documento` date DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_tipo` (`tipo`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL UNIQUE,
  `nombre` varchar(200) NOT NULL,
  `cif` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text,
  `ciudad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `pais` varchar(50) DEFAULT 'España',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `notas` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pedidos de venta
CREATE TABLE IF NOT EXISTS `pedidos_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_pedido` varchar(50) NOT NULL UNIQUE,
  `cliente_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('pendiente','pagado','enviado','completado','cancelado') NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `impuestos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notas` text,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_numero` (`numero_pedido`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_estado` (`estado`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de líneas de pedido de venta
CREATE TABLE IF NOT EXISTS `lineas_pedido_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedido` (`pedido_id`),
  KEY `idx_producto` (`producto_id`),
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos_venta` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de facturas
CREATE TABLE IF NOT EXISTS `facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_factura` varchar(50) NOT NULL UNIQUE,
  `pedido_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `estado` enum('pendiente','pagada','vencida','cancelada') NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `impuestos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_numero` (`numero_factura`),
  KEY `idx_pedido` (`pedido_id`),
  KEY `idx_cliente` (`cliente_id`),
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos_venta` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de líneas de factura
CREATE TABLE IF NOT EXISTS `lineas_factura` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factura_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT 1.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(5,2) NOT NULL DEFAULT 0.00,
  `impuesto` decimal(5,2) NOT NULL DEFAULT 21.00,
  `total` decimal(10,2) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_factura` (`factura_id`),
  KEY `idx_producto` (`producto_id`),
  FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de logs/auditoría
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `datos_anteriores` json DEFAULT NULL,
  `datos_nuevos` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_accion` (`accion`),
  KEY `idx_tabla` (`tabla`),
  KEY `idx_fecha` (`created_at`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de histórico de cambios de productos
CREATE TABLE IF NOT EXISTS `historico_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `campo` varchar(50) NOT NULL,
  `valor_anterior` text,
  `valor_nuevo` text,
  `motivo` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_producto` (`producto_id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_fecha` (`created_at`),
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de notificaciones
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('info','warning','danger','success') NOT NULL DEFAULT 'info',
  `titulo` varchar(200) NOT NULL,
  `mensaje` text,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `enlace` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_leida` (`leida`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pedidos de compra
CREATE TABLE IF NOT EXISTS `pedidos_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_pedido` varchar(50) NOT NULL UNIQUE,
  `proveedor_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_entrega_esperada` date DEFAULT NULL,
  `estado` enum('pendiente','recibido','parcial','completado','cancelado') NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `impuestos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `archivo_factura` varchar(255) DEFAULT NULL,
  `notas` text,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_numero` (`numero_pedido`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_estado` (`estado`),
  FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de líneas de pedido de compra
CREATE TABLE IF NOT EXISTS `lineas_pedido_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `cantidad_recibida` int(11) NOT NULL DEFAULT 0,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedido` (`pedido_id`),
  KEY `idx_producto` (`producto_id`),
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos_compra` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuración de empresa
CREATE TABLE IF NOT EXISTS `configuracion_empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL UNIQUE,
  `valor` text,
  `tipo` enum('texto','numero','booleano','json') NOT NULL DEFAULT 'texto',
  `descripcion` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración por defecto
INSERT INTO `configuracion_empresa` (`clave`, `valor`, `tipo`, `descripcion`) VALUES
('nombre_empresa', 'OrionERP', 'texto', 'Nombre de la empresa'),
('cif', '', 'texto', 'CIF de la empresa'),
('direccion', '', 'texto', 'Dirección de la empresa'),
('telefono', '', 'texto', 'Teléfono de contacto'),
('email', '', 'texto', 'Email de contacto'),
('iva_por_defecto', '21', 'numero', 'IVA por defecto en porcentaje'),
('moneda', 'EUR', 'texto', 'Moneda por defecto');

-- Insertar usuario administrador por defecto
-- Password: admin123 (hash bcrypt)
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `rol`, `activo`) VALUES
('Administrador', 'admin@orionerp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

COMMIT;

