<?php
/*
* ===================================================================
* JWT Middleware
*
* Middleware para validar tokens JWT en rutas protegidas
*/

namespace App\Middleware;

use App\Services\JWTService;
use App\Utils\Response;
use App\Utils\Logger;

class JWTMiddleware {
    private $jwtService;

    public function __construct() {
        $this->jwtService = new JWTService();
    }

    /*
    * ===================================================================
    * Valida el token JWT y agrega los datos del usuario a la request
    *
    * @return object|null Datos del usuario si el token es válido
    */
    public function handle(): ?object {
        $token = $this->jwtService->getTokenFromHeader();

        if (!$token) {
            Logger::warning('JWT token perdido', [
                'ip'  => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            ]);
            Response::unauthorized('Token no proporcionado');
            return null;
        }

        $decoded = $this->jwtService->validateToken($token);

        if (!$decoded) {
            Logger::warning('JWT token inválido', [
                'ip'  => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            ]);
            Response::unauthorized('Token inválido o expirado');
            return null;
        }

        // Agregar datos del usuario a la variable global para acceso posterior
        $GLOBALS['current_user'] = $decoded->data;

        return $decoded->data;
    }

    /*
    * ===================================================================
    * Obtiene el usuario actual de la request
    *
    * @return object|null
    */
    public static function getCurrentUser(): ?object {
        return $GLOBALS['current_user'] ?? null;
    }

    /*
    * ===================================================================
    * Verifica si el usuario tiene un tipo específico
    *
    * @param int $tipoUsuarioId
    * @return bool
    */
    public static function hasUserType(int $tipoUsuarioId): bool {
        $user = self::getCurrentUser();
        return $user && isset($user->idTipoUsuario) && $user->idTipoUsuario == $tipoUsuarioId;
    }

    /*
    * ===================================================================
    * Verifica si el usuario es administrador
    *
    * @return bool
    */
    public static function isAdmin(): bool {
        return self::hasUserType(1); // 1 = Administrador
    }

    /*
    * ===================================================================
    * Verifica si el usuario es Control Tiempo
    *
    * @return bool
    */
    public static function isControlTiempo(): bool {
        return self::hasUserType(2); // 2 = Control Tiempo
    }

    /*
    * ===================================================================
    * Verifica si el usuario tiene permiso (Admin o Control Tiempo)
    *
    * @return bool
    */
    public static function hasPermission(): bool {
        return self::isAdmin() || self::isControlTiempo();
    }
}
