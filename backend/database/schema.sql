-- ============================================
-- Sistema de Registro de Tiempos Muertos
-- Script de Creación de Base de Datos
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_detenciones CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_detenciones;

-- ============================================
-- TABLAS DEL SISTEMA
-- ============================================

-- Tipos de Usuario
CREATE TABLE IF NOT EXISTS usuarios_tipos (
    idTipoUsuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idTipoUsuario),
    UNIQUE KEY uk_tipo_usuario_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuarios
CREATE TABLE IF NOT EXISTS usuarios_listado (
    idUsuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idTipoUsuario INT UNSIGNED NOT NULL,
    Password VARCHAR(255) NOT NULL,
    UserName VARCHAR(255) NOT NULL,
    Nombre VARCHAR(255),
    ApellidoPat VARCHAR(255),
    ApellidoMat VARCHAR(255),
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idUsuario),
    UNIQUE KEY uk_usuario_username (UserName),
    UNIQUE KEY uk_usuario_nombre_apellidos (Nombre, ApellidoPat),
    FOREIGN KEY (idTipoUsuario) REFERENCES usuarios_tipos(idTipoUsuario) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Líneas
CREATE TABLE IF NOT EXISTS sistema_lineas (
    idLinea INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idLinea),
    UNIQUE KEY uk_linea_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Turnos
CREATE TABLE IF NOT EXISTS sistema_turnos (
    idTurnos INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idTurnos),
    UNIQUE KEY uk_turno_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Supervisores
CREATE TABLE IF NOT EXISTS sistema_supervisores (
    idSupervisor INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idSupervisor),
    UNIQUE KEY uk_supervisor_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos de Producción
CREATE TABLE IF NOT EXISTS sistema_tipo_produccion (
    idTipoProduccion INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idTipoProduccion),
    UNIQUE KEY uk_tipo_produccion_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Materiales
CREATE TABLE IF NOT EXISTS sistema_materiales (
    idMaterial INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Descripcion VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idMaterial),
    UNIQUE KEY uk_material_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rutas de Material
CREATE TABLE IF NOT EXISTS sistema_materiales_rutas (
    idMaterialRuta INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idMaterial INT UNSIGNED NOT NULL,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idMaterialRuta),
    UNIQUE KEY uk_material_ruta (idMaterial, Nombre),
    FOREIGN KEY (idMaterial) REFERENCES sistema_materiales(idMaterial) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rutas de Producción
CREATE TABLE IF NOT EXISTS sistema_rutas_produccion (
    idRutaProduccion INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idMaterial INT UNSIGNED NOT NULL,
    idMaterialRuta INT UNSIGNED NOT NULL,
    Nombre VARCHAR(255) NOT NULL,
    VelNominal INT UNSIGNED NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idRutaProduccion),
    UNIQUE KEY uk_ruta_produccion (idMaterial, idMaterialRuta, Nombre),
    FOREIGN KEY (idMaterial) REFERENCES sistema_materiales(idMaterial) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idMaterialRuta) REFERENCES sistema_materiales_rutas(idMaterialRuta) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos de Tiempos
CREATE TABLE IF NOT EXISTS sistema_tiempos_tipos (
    idTiemposTipo INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idTiemposTipo),
    UNIQUE KEY uk_tiempo_tipo_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Familias de Tiempos
CREATE TABLE IF NOT EXISTS sistema_tiempos_familia (
    idTiemposFamilia INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idTiemposFamilia),
    UNIQUE KEY uk_tiempo_familia_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tiempos
CREATE TABLE IF NOT EXISTS sistema_tiempos_listado (
    idTiempos INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idTiemposTipo INT UNSIGNED NOT NULL,
    idTiemposFamilia INT UNSIGNED NOT NULL,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idTiempos),
    UNIQUE KEY uk_tiempo (idTiemposTipo, idTiemposFamilia, Nombre),
    FOREIGN KEY (idTiemposTipo) REFERENCES sistema_tiempos_tipos(idTiemposTipo) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idTiemposFamilia) REFERENCES sistema_tiempos_familia(idTiemposFamilia) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estados de Detención
CREATE TABLE IF NOT EXISTS sistema_estado_detencion (
    idEstado INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(255) NOT NULL,
    Activo TINYINT(1) NOT NULL DEFAULT 1,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idEstado),
    UNIQUE KEY uk_estado_nombre (Nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Detenciones
CREATE TABLE IF NOT EXISTS detenciones (
    idDetencion INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idUsuario INT UNSIGNED NOT NULL,
    idLinea INT UNSIGNED NOT NULL,
    idTurnos INT UNSIGNED NOT NULL,
    idSupervisor INT UNSIGNED NOT NULL,
    idTipoProduccion INT UNSIGNED NOT NULL,
    Fecha TIMESTAMP NOT NULL DEFAULT '1970-01-01 00:00:01',
    idEstado INT UNSIGNED NOT NULL,
    Observaciones TEXT,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idDetencion),
    UNIQUE KEY uk_detencion (idLinea, idTurnos, idTipoProduccion, Fecha),
    FOREIGN KEY (idUsuario) REFERENCES usuarios_listado(idUsuario) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idLinea) REFERENCES sistema_lineas(idLinea) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idTurnos) REFERENCES sistema_turnos(idTurnos) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idSupervisor) REFERENCES sistema_supervisores(idSupervisor) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idTipoProduccion) REFERENCES sistema_tipo_produccion(idTipoProduccion) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idEstado) REFERENCES sistema_estado_detencion(idEstado) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Detenciones - Órdenes de Fabricación
CREATE TABLE IF NOT EXISTS detenciones_of (
    idDetencionOF INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idDetencion INT UNSIGNED NOT NULL,
    idMaterial INT UNSIGNED NOT NULL,
    idMaterialRuta INT UNSIGNED NOT NULL,
    idRutaProduccion INT UNSIGNED NOT NULL,
    CantidadProd INT UNSIGNED NOT NULL,
    Lote VARCHAR(255) NOT NULL,
    Observaciones TEXT,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idDetencionOF),
    UNIQUE KEY uk_detencion_of (idMaterial, idMaterialRuta, Lote),
    FOREIGN KEY (idDetencion) REFERENCES detenciones(idDetencion) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idMaterial) REFERENCES sistema_materiales(idMaterial) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idMaterialRuta) REFERENCES sistema_materiales_rutas(idMaterialRuta) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idRutaProduccion) REFERENCES sistema_rutas_produccion(idRutaProduccion) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Detenciones - Órdenes de Fabricación - Detalle
CREATE TABLE IF NOT EXISTS detenciones_of_detalle (
    idDetalles INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idDetencionOF INT UNSIGNED NOT NULL,
    idDetencion INT UNSIGNED NOT NULL,
    idTiempos INT UNSIGNED NOT NULL,
    Fecha TIMESTAMP NOT NULL DEFAULT '1970-01-01 00:00:01',
    HoraInicio TIME NOT NULL DEFAULT '00:00:00',
    HoraTermino TIME NOT NULL DEFAULT '00:00:00',
    Minutos INT UNSIGNED NOT NULL,
    Observaciones TEXT,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FechaModificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (idDetalles),
    UNIQUE KEY uk_detencion_detalle (idDetencionOF, idTiempos, Fecha, HoraInicio, HoraTermino),
    FOREIGN KEY (idDetencionOF) REFERENCES detenciones_of(idDetencionOF) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idDetencion) REFERENCES detenciones(idDetencion) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (idTiempos) REFERENCES sistema_tiempos_listado(idTiempos) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ÍNDICES ADICIONALES PARA RENDIMIENTO
-- ============================================

CREATE INDEX idx_detencion_estado ON detenciones(idEstado);
CREATE INDEX idx_detencion_usuario ON detenciones(idUsuario);
CREATE INDEX idx_detencion_fecha ON detenciones(Fecha);
CREATE INDEX idx_detencion_of_detencion ON detenciones_of(idDetencion);
CREATE INDEX idx_detencion_detalle_detencion ON detenciones_of_detalle(idDetencion);
CREATE INDEX idx_detencion_detalle_fecha ON detenciones_of_detalle(Fecha);
