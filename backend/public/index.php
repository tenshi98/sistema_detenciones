<?php
/*
* ===================================================================
* Punto de entrada principal de la API
*/

// Mostrar errores en desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar configuración
use App\Config\Config;
use App\Middleware\CORSMiddleware;
use App\Utils\Logger;
use App\Utils\Response;

try {
    // Inicializar configuración
    Config::getInstance();

    // Manejar CORS
    CORSMiddleware::handle();

    // Cargar rutas
    $router = require_once __DIR__ . '/../config/routes.php';

    // Despachar ruta
    $router->dispatch();
} catch (\Exception $e) {
    // Log de error
    Logger::exception($e);

    // Respuesta de error
    $debug = Config::getInstance()->get('app.debug', false);

    if ($debug) {
        Response::serverError($e->getMessage());
    } else {
        Response::serverError('Error interno del servidor');
    }
}
