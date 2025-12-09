<?php
/*
* ===================================================================
* Logger Utility
*
* Sistema de logging con niveles INFO, WARNING, ERROR
* Soporta rotación de archivos y formato JSON estructurado
*/

namespace App\Utils;

use App\Config\Config;

class Logger {
    const LEVEL_INFO    = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR   = 'ERROR';

    private static $logPath;
    private static $logLevel;

    /*
    * ===================================================================
    * Inicializa el logger
    */
    private static function init(): void {
        if (self::$logPath === null) {
            $config          = Config::getInstance();
            self::$logPath   = $config->get('logging.path', 'logs/');
            self::$logLevel  = $config->get('logging.level', 'INFO');

            // Crear directorio de logs si no existe
            if (!is_dir(self::$logPath)) {
                mkdir(self::$logPath, 0755, true);
            }
        }
    }

    /*
    * ===================================================================
    * Registra un mensaje de nivel INFO
    *
    * @param string $message
    * @param array $context
    */
    public static function info(string $message, array $context = []): void {
        self::log(self::LEVEL_INFO, $message, $context);
    }

    /*
    * ===================================================================
    * Registra un mensaje de nivel WARNING
    *
    * @param string $message
    * @param array $context
    */
    public static function warning(string $message, array $context = []): void {
        self::log(self::LEVEL_WARNING, $message, $context);
    }

    /*
    * ===================================================================
    * Registra un mensaje de nivel ERROR
    *
    * @param string $message
    * @param array $context
    */
    public static function error(string $message, array $context = []): void {
        self::log(self::LEVEL_ERROR, $message, $context);
    }

    /*
    * ===================================================================
    * Registra un mensaje en el archivo de log
    *
    * @param string $level
    * @param string $message
    * @param array $context
    */
    private static function log(string $level, string $message, array $context = []): void {
        self::init();

        // Verificar si el nivel debe ser registrado
        if (!self::shouldLog($level)) {
            return;
        }

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level'     => $level,
            'message'   => $message,
            'context'   => $context,
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            'method'    => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'uri'       => $_SERVER['REQUEST_URI'] ?? 'CLI',
        ];

        // Nombre del archivo basado en la fecha
        $filename = self::$logPath . date('Y-m-d') . '.log';

        // Escribir en formato JSON (una línea por entrada)
        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);

        // Rotar logs si es necesario
        self::rotateLogs();
    }

    /*
    * ===================================================================
    * Verifica si un nivel debe ser registrado
    *
    * @param string $level
    * @return bool
    */
    private static function shouldLog(string $level): bool {
        $levels = [
            self::LEVEL_INFO    => 1,
            self::LEVEL_WARNING => 2,
            self::LEVEL_ERROR   => 3,
        ];

        $currentLevel = $levels[self::$logLevel] ?? 1;
        $messageLevel = $levels[$level] ?? 1;

        return $messageLevel >= $currentLevel;
    }

    /*
    * ===================================================================
    * Rota los archivos de log antiguos (mantiene últimos 30 días)
    */
    private static function rotateLogs(): void {
        $files = glob(self::$logPath . '*.log');
        $maxAge = 30 * 24 * 60 * 60; // 30 días en segundos

        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > $maxAge) {
                unlink($file);
            }
        }
    }

    /*
    * ===================================================================
    * Registra una excepción
    *
    * @param \Exception $exception
    */
    public static function exception(\Exception $exception): void {
        self::error('Exception caught', [
            'message' => $exception->getMessage(),
            'code'    => $exception->getCode(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'trace'   => $exception->getTraceAsString(),
        ]);
    }
}
