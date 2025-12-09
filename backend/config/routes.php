<?php
/*
* ===================================================================
* Routes Definition
*
* Define todas las rutas de la aplicación
*/

use App\Config\Router;

$router = new Router();

// ============================================
// RUTAS PÚBLICAS (sin protección JWT)
// ============================================

// Autenticación
$router->post('/auth/login', 'AuthController', 'login', false);
$router->post('/auth/register', 'AuthController', 'register', false);
$router->post('/auth/refresh', 'AuthController', 'refresh', false);

// ============================================
// RUTAS PROTEGIDAS (requieren JWT)
// ============================================

// Tipos de Usuario
$router->get('/userTypes', 'UserTypeController', 'index');
$router->get('/userTypes/{id}', 'UserTypeController', 'show');
$router->post('/userTypes', 'UserTypeController', 'store');
$router->put('/userTypes/{id}', 'UserTypeController', 'update');
$router->delete('/userTypes/{id}', 'UserTypeController', 'destroy');
$router->post('/userTypes/{id}/activate', 'UserTypeController', 'activate');

// Usuarios
$router->get('/users', 'UserController', 'index');
$router->get('/users/{id}', 'UserController', 'show');
$router->post('/users', 'UserController', 'store');
$router->put('/users/{id}', 'UserController', 'update');
$router->delete('/users/{id}', 'UserController', 'destroy');
$router->post('/users/{id}/activate', 'UserController', 'activate');

// Líneas
$router->get('/lineas', 'LineaController', 'index');
$router->get('/lineas/{id}', 'LineaController', 'show');
$router->post('/lineas', 'LineaController', 'store');
$router->put('/lineas/{id}', 'LineaController', 'update');
$router->delete('/lineas/{id}', 'LineaController', 'destroy');
$router->post('/lineas/{id}/activate', 'LineaController', 'activate');

// Turnos
$router->get('/turnos', 'TurnoController', 'index');
$router->get('/turnos/{id}', 'TurnoController', 'show');
$router->post('/turnos', 'TurnoController', 'store');
$router->put('/turnos/{id}', 'TurnoController', 'update');
$router->delete('/turnos/{id}', 'TurnoController', 'destroy');
$router->post('/turnos/{id}/activate', 'TurnoController', 'activate');

// Supervisores
$router->get('/supervisor', 'SupervisorController', 'index');
$router->get('/supervisor/{id}', 'SupervisorController', 'show');
$router->post('/supervisor', 'SupervisorController', 'store');
$router->put('/supervisor/{id}', 'SupervisorController', 'update');
$router->delete('/supervisor/{id}', 'SupervisorController', 'destroy');
$router->post('/supervisor/{id}/activate', 'SupervisorController', 'activate');

// Tipos de Producción
$router->get('/tipoProduccion', 'TipoProduccionController', 'index');
$router->get('/tipoProduccion/{id}', 'TipoProduccionController', 'show');
$router->post('/tipoProduccion', 'TipoProduccionController', 'store');
$router->put('/tipoProduccion/{id}', 'TipoProduccionController', 'update');
$router->delete('/tipoProduccion/{id}', 'TipoProduccionController', 'destroy');
$router->post('/tipoProduccion/{id}/activate', 'TipoProduccionController', 'activate');

// Materiales
$router->get('/material', 'MaterialController', 'index');
$router->get('/material/{id}', 'MaterialController', 'show');
$router->post('/material', 'MaterialController', 'store');
$router->put('/material/{id}', 'MaterialController', 'update');
$router->delete('/material/{id}', 'MaterialController', 'destroy');
$router->post('/material/{id}/activate', 'MaterialController', 'activate');

// Rutas Material
$router->get('/rutaMaterial', 'RutaMaterialController', 'index');
$router->get('/rutaMaterial/{id}', 'RutaMaterialController', 'show');
$router->post('/rutaMaterial', 'RutaMaterialController', 'store');
$router->put('/rutaMaterial/{id}', 'RutaMaterialController', 'update');
$router->delete('/rutaMaterial/{id}', 'RutaMaterialController', 'destroy');
$router->post('/rutaMaterial/{id}/activate', 'RutaMaterialController', 'activate');

// Rutas Producción
$router->get('/rutaProduccion', 'RutaProduccionController', 'index');
$router->get('/rutaProduccion/{id}', 'RutaProduccionController', 'show');
$router->post('/rutaProduccion', 'RutaProduccionController', 'store');
$router->put('/rutaProduccion/{id}', 'RutaProduccionController', 'update');
$router->delete('/rutaProduccion/{id}', 'RutaProduccionController', 'destroy');
$router->post('/rutaProduccion/{id}/activate', 'RutaProduccionController', 'activate');

// Tipos de Tiempos
$router->get('/tiposTiempo', 'TipoTiempoController', 'index');
$router->get('/tiposTiempo/{id}', 'TipoTiempoController', 'show');
$router->post('/tiposTiempo', 'TipoTiempoController', 'store');
$router->put('/tiposTiempo/{id}', 'TipoTiempoController', 'update');
$router->delete('/tiposTiempo/{id}', 'TipoTiempoController', 'destroy');
$router->post('/tiposTiempo/{id}/activate', 'TipoTiempoController', 'activate');

// Familias de Tiempos
$router->get('/familiasTiempo', 'FamiliaTiempoController', 'index');
$router->get('/familiasTiempo/{id}', 'FamiliaTiempoController', 'show');
$router->post('/familiasTiempo', 'FamiliaTiempoController', 'store');
$router->put('/familiasTiempo/{id}', 'FamiliaTiempoController', 'update');
$router->delete('/familiasTiempo/{id}', 'FamiliaTiempoController', 'destroy');
$router->post('/familiasTiempo/{id}/activate', 'FamiliaTiempoController', 'activate');

// Tiempos
$router->get('/tiempos', 'TiempoController', 'index');
$router->get('/tiempos/{id}', 'TiempoController', 'show');
$router->post('/tiempos', 'TiempoController', 'store');
$router->put('/tiempos/{id}', 'TiempoController', 'update');
$router->delete('/tiempos/{id}', 'TiempoController', 'destroy');
$router->post('/tiempos/{id}/activate', 'TiempoController', 'activate');

// Estados de Detención
$router->get('/detencionesEstados', 'EstadoDetencionController', 'index');
$router->get('/detencionesEstados/{id}', 'EstadoDetencionController', 'show');
$router->post('/detencionesEstados', 'EstadoDetencionController', 'store');
$router->put('/detencionesEstados/{id}', 'EstadoDetencionController', 'update');
$router->delete('/detencionesEstados/{id}', 'EstadoDetencionController', 'destroy');
$router->post('/detencionesEstados/{id}/activate', 'EstadoDetencionController', 'activate');

// Detenciones
$router->get('/detenciones', 'DetencionController', 'index');
$router->get('/detenciones/abiertas', 'DetencionController', 'abiertas');
$router->get('/detenciones/{id}', 'DetencionController', 'show');
$router->post('/detenciones', 'DetencionController', 'store');
$router->put('/detenciones/{id}', 'DetencionController', 'update');

// Detenciones OF
$router->get('/detencionesOF', 'DetencionOFController', 'index');
$router->get('/detencionesOF/detencion/{id}', 'DetencionOFController', 'byDetencion');
$router->get('/detencionesOF/{id}', 'DetencionOFController', 'show');
$router->post('/detencionesOF', 'DetencionOFController', 'store');
$router->put('/detencionesOF/{id}', 'DetencionOFController', 'update');

// Detenciones OF Detalle
$router->get('/detencionesOFDetalle', 'DetencionOFDetalleController', 'index');
$router->get('/detencionesOFDetalle/of/{id}', 'DetencionOFDetalleController', 'byOF');
$router->get('/detencionesOFDetalle/resumen/{id}', 'DetencionOFDetalleController', 'resumen');
$router->get('/detencionesOFDetalle/porcentajes/{id}', 'DetencionOFDetalleController', 'porcentajes');
$router->get('/detencionesOFDetalle/{id}', 'DetencionOFDetalleController', 'show');
$router->post('/detencionesOFDetalle', 'DetencionOFDetalleController', 'store');
$router->put('/detencionesOFDetalle/{id}', 'DetencionOFDetalleController', 'update');
$router->delete('/detencionesOFDetalle/{id}', 'DetencionOFDetalleController', 'destroy');

// Endpoints de exportacon de datos
$router->get('/export/detencion/{id}', 'ExportController', 'exportDetencion');
$router->get('/export/detenciones', 'ExportController', 'exportDetenciones');
$router->get('/export/resumen', 'ExportController', 'exportResumenGeneral');

return $router;
