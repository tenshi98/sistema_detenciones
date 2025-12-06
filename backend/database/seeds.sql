-- ============================================
-- Sistema de Registro de Tiempos Muertos
-- Script de Datos Precargados (Seeds)
-- ============================================

USE sistema_detenciones;

-- ============================================
-- TIPOS DE USUARIO
-- ============================================
INSERT INTO usuarios_tipos (Nombre) VALUES
('Administrador'),
('Control Tiempo'),
('Supervisor'),
('Usuario Normal');

-- ============================================
-- USUARIO ADMINISTRADOR POR DEFECTO
-- Password: admin123 (hash bcrypt)
-- ============================================
INSERT INTO usuarios_listado (idTipoUsuario, UserName, Password, Nombre, ApellidoPat, ApellidoMat) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 'Principal');

-- ============================================
-- TURNOS
-- ============================================
INSERT INTO sistema_turnos (Nombre) VALUES
('A'),
('B'),
('C'),
('AB'),
('BC');

-- ============================================
-- TIPOS DE PRODUCCIÓN
-- ============================================
INSERT INTO sistema_tipo_produccion (Nombre) VALUES
('Normal'),
('Paralela');

-- ============================================
-- TIPOS DE TIEMPOS
-- ============================================
INSERT INTO sistema_tiempos_tipos (Nombre) VALUES
('Produccion'),
('Preparacion'),
('Muerto');

-- ============================================
-- FAMILIAS DE TIEMPOS
-- ============================================
INSERT INTO sistema_tiempos_familia (Nombre) VALUES
('Producción Sobre Nominal'),
('Producción Bajo Nominal'),
('Ajustes'),
('Averías'),
('Material defectuoso'),
('Fallas operacionales'),
('Corte de suministros'),
('Atrasos'),
('Externos'),
('No aplica');

-- ============================================
-- ESTADOS DE DETENCIÓN
-- ============================================
INSERT INTO sistema_estado_detencion (Nombre) VALUES
('Abierta'),
('Cerrada');

-- ============================================
-- DATOS DE EJEMPLO (OPCIONAL)
-- Descomentar si se desea tener datos de prueba
-- ============================================

-- Líneas de ejemplo
-- INSERT INTO sistema_lineas (Nombre) VALUES
-- ('Línea 1'),
-- ('Línea 2'),
-- ('Línea 3');

-- Supervisores de ejemplo
-- INSERT INTO sistema_supervisores (Nombre) VALUES
-- ('Juan Pérez'),
-- ('María González'),
-- ('Carlos Rodríguez');

-- Materiales de ejemplo
-- INSERT INTO sistema_materiales (Nombre, Descripcion) VALUES
-- ('Material A', 'Descripción del Material A'),
-- ('Material B', 'Descripción del Material B');

-- Rutas de Material (requiere materiales)
-- INSERT INTO sistema_materiales_rutas (idMaterial, Nombre) VALUES
-- (1, 'Directo'),
-- (1, 'Embotellado'),
-- (1, 'Etiquetado'),
-- (2, 'Directo'),
-- (2, 'Embotellado');

-- Rutas de Producción (requiere materiales y rutas de material)
-- INSERT INTO sistema_rutas_produccion (idMaterial, idMaterialRuta, Nombre, VelNominal) VALUES
-- (1, 1, 'Ruta Estándar', 100),
-- (1, 2, 'Ruta Rápida', 150),
-- (2, 4, 'Ruta Normal', 120);

-- Tiempos (requiere tipos y familias)
-- INSERT INTO sistema_tiempos_listado (idTiemposTipo, idTiemposFamilia, Nombre) VALUES
-- (1, 1, 'Producción Normal'),
-- (1, 2, 'Producción Lenta'),
-- (2, 3, 'Ajuste de Máquina'),
-- (3, 4, 'Avería Mecánica'),
-- (3, 5, 'Material Defectuoso');
