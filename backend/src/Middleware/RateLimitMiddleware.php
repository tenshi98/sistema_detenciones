<?php
/*
* Rate Limit Middleware
*
* Middleware para controlar el rate limiting por IP y usuario
*/

namespace App\Middleware;

use App\Config\Config;
use App\Utils\Response;
use App\Utils\Logger;

class RateLimitMiddleware {
    private $maxRequests;
    private $windowSeconds;
    private $storageFile;

    public function __construct() {
        $config              = Config::getInstance();
        $this->maxRequests   = $config->get('rate_limit.requests', 100);
        $this->windowSeconds = $config->get('rate_limit.window', 60);
        $this->storageFile   = __DIR__ . '/../../storage/rate_limit.json';

        // Crear directorio de storage si no existe
        $storageDir = dirname($this->storageFile);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
    }

    /*
    * Verifica el rate limit
    *
    * @return bool True si está dentro del límite
    */
    public function handle(): bool {
        $identifier = $this->getIdentifier();
        $data       = $this->loadData();
        $now        = time();

        // Limpiar datos antiguos
        $data = $this->cleanOldData($data, $now);

        // Inicializar si no existe
        if (!isset($data[$identifier])) {
            $data[$identifier] = [
                'count'        => 0,
                'window_start' => $now,
            ];
        }

        $userLimit = &$data[$identifier];

        // Resetear ventana si ha pasado el tiempo
        if ($now - $userLimit['window_start'] > $this->windowSeconds) {
            $userLimit['count']        = 0;
            $userLimit['window_start'] = $now;
        }

        // Incrementar contador
        $userLimit['count']++;

        // Guardar datos
        $this->saveData($data);

        // Verificar límite
        if ($userLimit['count'] > $this->maxRequests) {
            Logger::warning('Rate limit excedido', [
                'identifier' => $identifier,
                'count'      => $userLimit['count'],
                'max'        => $this->maxRequests,
            ]);

            Response::error(
                'Demasiadas solicitudes. Por favor intente más tarde.',
                null,
                429
            );
            return false;
        }

        return true;
    }

    /*
    * Obtiene el identificador único (IP o usuario)
    *
    * @return string
    */
    private function getIdentifier(): string {
        // Intentar obtener usuario actual
        $user = JWTMiddleware::getCurrentUser();
        if ($user && isset($user->idUsuario)) {
            return 'user_' . $user->idUsuario;
        }

        // Usar IP como fallback
        return 'ip_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }

    /*
    * Carga los datos de rate limit
    *
    * @return array
    */
    private function loadData(): array {
        if (!file_exists($this->storageFile)) {
            return [];
        }

        $content = file_get_contents($this->storageFile);
        return json_decode($content, true) ?? [];
    }

    /*
    * Guarda los datos de rate limit
    *
    * @param array $data
    */
    private function saveData(array $data): void {
        file_put_contents(
            $this->storageFile,
            json_encode($data, JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    /*
    * Limpia datos antiguos (más de 2 ventanas)
    *
    * @param array $data
    * @param int $now
    * @return array
    */
    private function cleanOldData(array $data, int $now): array {
        $maxAge = $this->windowSeconds * 2;

        foreach ($data as $key => $value) {
            if ($now - $value['window_start'] > $maxAge) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
