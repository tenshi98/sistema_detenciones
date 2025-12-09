<?php
/*
* ===================================================================
* Configuration Loader
*
* Carga las variables de entorno desde el archivo .env
* y proporciona acceso a la configuración de la aplicación
*/

namespace App\Config;

use Dotenv\Dotenv;

class Config {
    // Variables
    private static $instance = null;
    private $config          = [];

    private function __construct() {
        // Cargar variables de entorno
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // Configuración de base de datos
        $this->config['database'] = [
            'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
            'host'      => $_ENV['DB_HOST'] ?? 'localhost',
            'port'      => $_ENV['DB_PORT'] ?? 3306,
            'database'  => $_ENV['DB_NAME'] ?? 'sistema_detenciones',
            'username'  => $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ];

        // Configuración JWT
        $this->config['jwt'] = [
            'secret'         => $_ENV['JWT_SECRET'] ?? 'default-secret-key',
            'expire'         => (int)($_ENV['JWT_EXPIRE'] ?? 3600),
            'refresh_expire' => (int)($_ENV['JWT_REFRESH_EXPIRE'] ?? 86400),
            'algorithm'      => 'HS256',
        ];

        // Configuración de aplicación
        $this->config['app'] = [
            'env'   => $_ENV['APP_ENV'] ?? 'production',
            'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'url'   => $_ENV['APP_URL'] ?? 'http://localhost:8000',
        ];

        // Configuración CORS
        $this->config['cors'] = [
            'allowed_origins' => explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? '*'),
        ];

        // Configuración Rate Limiting
        $this->config['rate_limit'] = [
            'requests' => (int)($_ENV['RATE_LIMIT_REQUESTS'] ?? 100),
            'window'   => (int)($_ENV['RATE_LIMIT_WINDOW'] ?? 60),
        ];

        // Configuración de Logging
        $this->config['logging'] = [
            'level' => $_ENV['LOG_LEVEL'] ?? 'INFO',
            'path'  => $_ENV['LOG_PATH'] ?? 'logs/',
        ];
    }

    /*
    * ===================================================================
    * Obtiene la instancia singleton de Config
    */
    public static function getInstance(): Config {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /*
    * ===================================================================
    * Obtiene un valor de configuración
    *
    * @param string $key Clave en formato 'seccion.clave'
    * @param mixed $default Valor por defecto si no existe
    * @return mixed
    */
    public function get(string $key, $default = null) {
        // Variables
        $keys  = explode('.', $key);
        $value = $this->config;
        // Recorro
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        // Devuelvo
        return $value;
    }

    /*
    * ===================================================================
    * Obtiene toda la configuración
    */
    public function all(): array {
        return $this->config;
    }
}
