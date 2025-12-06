<?php
/*
* CORS Middleware
*
* Middleware para manejar CORS (Cross-Origin Resource Sharing)
*/

namespace App\Middleware;

use App\Config\Config;

class CORSMiddleware {
    /*
    * Maneja las cabeceras CORS
    */
    public static function handle(): void {
        $config = Config::getInstance();
        $allowedOrigins = $config->get('cors.allowed_origins', ['*']);

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // Verificar si el origen está permitido
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header('Access-Control-Allow-Origin: ' . ($origin ?: '*'));
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Max-Age: 86400');

        // Manejar preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
