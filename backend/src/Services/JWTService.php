<?php
/*
* ===================================================================
* JWT Service
*
* Servicio para generación y validación de tokens JWT
*/

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\Config;
use App\Utils\Logger;

class JWTService {
    private $secret;
    private $algorithm;
    private $expire;
    private $refreshExpire;

    public function __construct() {
        $config              = Config::getInstance();
        $this->secret        = $config->get('jwt.secret');
        $this->algorithm     = $config->get('jwt.algorithm');
        $this->expire        = $config->get('jwt.expire');
        $this->refreshExpire = $config->get('jwt.refresh_expire');
    }

    /*
    * ===================================================================
    * Genera un token JWT
    *
    * @param array $payload Datos a incluir en el token
    * @param bool $isRefresh Si es un refresh token
    * @return string
    */
    public function generateToken(array $payload, bool $isRefresh = false): string {
        $issuedAt = time();
        $expire = $isRefresh ? $this->refreshExpire : $this->expire;

        $data = [
            'iat'  => $issuedAt,
            'exp'  => $issuedAt + $expire,
            'data' => $payload,
        ];

        try {
            $token = JWT::encode($data, $this->secret, $this->algorithm);

            Logger::info('JWT token generated', [
                'user_id'    => $payload['idUsuario'] ?? null,
                'is_refresh' => $isRefresh,
            ]);

            return $token;
        } catch (\Exception $e) {
            Logger::error('JWT generation failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /*
    * ===================================================================
    * Valida y decodifica un token JWT
    *
    * @param string $token
    * @return object|null
    */
    public function validateToken(string $token): ?object {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));

            Logger::info('JWT token validated', [
                'user_id' => $decoded->data->idUsuario ?? null,
            ]);

            return $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            Logger::warning('JWT token expired', [
                'error' => $e->getMessage(),
            ]);
            return null;
        } catch (\Exception $e) {
            Logger::error('JWT validation failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /*
    * ===================================================================
    * Extrae el token del header Authorization
    *
    * @return string|null
    */
    public function getTokenFromHeader(): ?string {
        $headers    = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /*
    * ===================================================================
    * Verifica si un token está expirado
    *
    * @param string $token
    * @return bool
    */
    public function isExpired(string $token): bool {
        try {
            JWT::decode($token, new Key($this->secret, $this->algorithm));
            return false;
        } catch (\Firebase\JWT\ExpiredException $e) {
            return true;
        } catch (\Exception $e) {
            return true;
        }
    }

    /*
    * ===================================================================
    * Renueva un token
    *
    * @param string $refreshToken
    * @return array|null Array con nuevo access token y refresh token
    */
    public function refreshToken(string $refreshToken): ?array {
        $decoded = $this->validateToken($refreshToken);

        if (!$decoded) {
            return null;
        }

        $payload = (array) $decoded->data;

        return [
            'access_token' => $this->generateToken($payload, false),
            'refresh_token' => $this->generateToken($payload, true),
        ];
    }
}
