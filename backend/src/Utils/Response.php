<?php
/*
* Response Helper
*
* Utilidad para generar respuestas JSON estandarizadas
*/

namespace App\Utils;

class Response {
    /*
    * Envía una respuesta JSON exitosa
    *
    * @param mixed $data
    * @param string $message
    * @param int $statusCode
    */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): void {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /*
    * Envía una respuesta JSON de error
    *
    * @param string $message
    * @param mixed $errors
    * @param int $statusCode
    */
    public static function error(string $message = 'Error', $errors = null, int $statusCode = 400): void {
        self::json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }

    /*
    * Envía una respuesta JSON
    *
    * @param array $data
    * @param int $statusCode
    */
    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /*
    * Envía una respuesta 404
    */
    public static function notFound(string $message = 'Resource not found'): void {
        self::error($message, null, 404);
    }

    /*
    * Envía una respuesta 401
    */
    public static function unauthorized(string $message = 'Unauthorized'): void {
        self::error($message, null, 401);
    }

    /*
    * Envía una respuesta 403
    */
    public static function forbidden(string $message = 'Forbidden'): void {
        self::error($message, null, 403);
    }

    /*
    * Envía una respuesta 500
    */
    public static function serverError(string $message = 'Internal server error'): void {
        self::error($message, null, 500);
    }

    /*
    * Envía una respuesta de validación fallida
    *
    * @param array $errors
    */
    public static function validationError(array $errors): void {
        self::error('Validation failed', $errors, 422);
    }
}
