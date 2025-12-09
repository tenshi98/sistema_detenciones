<?php
/*
* ===================================================================
* Router
*
* Sistema de enrutamiento de la aplicación
*/

namespace App\Config;

use App\Middleware\JWTMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Utils\Response;

class Router{
    // Variables
    private $routes = [];
    private $jwtMiddleware;
    private $rateLimitMiddleware;

    public function __construct() {
        $this->jwtMiddleware       = new JWTMiddleware();
        $this->rateLimitMiddleware = new RateLimitMiddleware();
    }

    /*
    * ===================================================================
    * Registra una ruta GET
    */
    public function get(string $path, string $controller, string $method, bool $protected = true): void {
        $this->addRoute('GET', $path, $controller, $method, $protected);
    }

    /*
    * ===================================================================
    * Registra una ruta POST
    */
    public function post(string $path, string $controller, string $method, bool $protected = true): void {
        $this->addRoute('POST', $path, $controller, $method, $protected);
    }

    /*
    * ===================================================================
    * Registra una ruta PUT
    */
    public function put(string $path, string $controller, string $method, bool $protected = true): void {
        $this->addRoute('PUT', $path, $controller, $method, $protected);
    }

    /*
    * ===================================================================
    * Registra una ruta DELETE
    */
    public function delete(string $path, string $controller, string $method, bool $protected = true): void {
        $this->addRoute('DELETE', $path, $controller, $method, $protected);
    }

    /*
    * ===================================================================
    * Agrega una ruta
    */
    private function addRoute(string $httpMethod, string $path, string $controller, string $method, bool $protected): void {
        $this->routes[] = [
            'http_method' => $httpMethod,
            'path'        => $path,
            'controller'  => $controller,
            'method'      => $method,
            'protected'   => $protected,
        ];
    }

    /*
    * ===================================================================
    * Despacha la ruta actual
    */
    public function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remover /api/ del inicio si existe
        $requestUri = preg_replace('#^/api#', '', $requestUri);

        foreach ($this->routes as $route) {
            $pattern = $this->convertToRegex($route['path']);

            if ($route['http_method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                // Aplicar rate limiting
                $this->rateLimitMiddleware->handle();

                // Aplicar JWT middleware si la ruta está protegida
               if ($route['protected']) {
                    $this->jwtMiddleware->handle();
                }

                // Extraer parámetros de la URL
                array_shift($matches); // Remover el match completo

                // Instanciar controlador y ejecutar método
                $controllerClass = "App\\Controllers\\{$route['controller']}";
                $controller      = new $controllerClass();
                $methodName      = $route['method'];

                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        // Ruta no encontrada
        Response::notFound('Endpoint no encontrado');
    }

    /*
    * ===================================================================
    * Convierte un path a regex
    */
    private function convertToRegex(string $path): string {
        // Convertir {id} a (\d+)
        $pattern = preg_replace('/\{(\w+)\}/', '(\d+)', $path);
        return '#^' . $pattern . '$#';
    }
}
