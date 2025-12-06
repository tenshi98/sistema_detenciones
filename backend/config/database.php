<?php
/*
* Database Connection Manager
*
* Gestiona las conexiones a la base de datos usando PDO
* Permite fácil migración entre diferentes motores de base de datos
*/

namespace App\Config;

use PDO;
use PDOException;
use App\Utils\Logger;

class Database {
    // Variables
    private static $instance = null;
    private $connection      = null;
    private $config;

    private function __construct() {
        $this->config = Config::getInstance();
    }

    /*
    * Obtiene la instancia singleton de Database
    */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /*
    * Obtiene la conexión PDO
    *
    * @return PDO
    * @throws PDOException
    */
    public function getConnection(): PDO {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /*
    * Establece la conexión a la base de datos
    *
    * @throws PDOException
    */
    private function connect(): void {
        try {
            $driver   = $this->config->get('database.driver');
            $dsn      = $this->buildDSN($driver);
            $username = $this->config->get('database.username');
            $password = $this->config->get('database.password');

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ];

            // Configuraciones específicas por driver
            if ($driver === 'mysql') {
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
            }

            $this->connection = new PDO($dsn, $username, $password, $options);

            Logger::info('Conexión a la base de datos establecida', [
                'driver'   => $driver,
                'host'     => $this->config->get('database.host'),
                'database' => $this->config->get('database.database'),
            ]);
        } catch (PDOException $e) {
            Logger::error('Falla en la Conexión a la base de datos', [
                'error' => $e->getMessage(),
                'code'  => $e->getCode(),
            ]);
            throw $e;
        }
    }

    /*
    * Construye el DSN según el driver de base de datos
    *
    * @param string $driver
    * @return string
    */
    private function buildDSN(string $driver): string {
        $host     = $this->config->get('database.host');
        $port     = $this->config->get('database.port');
        $database = $this->config->get('database.database');
        $charset  = $this->config->get('database.charset');

        switch ($driver) {
            case 'mysql':
                return "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

            case 'pgsql':
            case 'postgresql':
                return "pgsql:host={$host};port={$port};dbname={$database}";

            case 'sqlsrv':
            case 'mssql':
                return "sqlsrv:Server={$host},{$port};Database={$database}";

            case 'sqlite':
                return "sqlite:{$database}";

            default:
                throw new \InvalidArgumentException("Unsupported database driver: {$driver}");
        }
    }

    /*
    * Inicia una transacción
    */
    public function beginTransaction(): bool {
        return $this->getConnection()->beginTransaction();
    }

    /*
    * Confirma una transacción
    */
    public function commit(): bool {
        return $this->getConnection()->commit();
    }

    /*
    * Revierte una transacción
    */
    public function rollback(): bool {
        return $this->getConnection()->rollBack();
    }

    /*
    * Verifica si hay una transacción activa
    */
    public function inTransaction(): bool {
        return $this->getConnection()->inTransaction();
    }

    /*
    * Cierra la conexión
    */
    public function disconnect(): void {
        $this->connection = null;
        Logger::info('Conexión a la base de datos cerrada');
    }

    /*
    * Previene la clonación del objeto
    */
    private function __clone() {}

    /*
    * Previene la deserialización del objeto
    */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
