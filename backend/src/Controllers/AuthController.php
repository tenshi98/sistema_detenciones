<?php
/*
* ===================================================================
* Auth Controller
*
* Controlador de autenticación (login, registro, refresh token)
*/

namespace App\Controllers;

use App\Models\User;
use App\Services\JWTService;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class AuthController {
    // Variables
    private $userModel;
    private $jwtService;

    public function __construct() {
        $this->userModel  = new User();
        $this->jwtService = new JWTService();
    }

    /*
    * ===================================================================
    * Login de usuario
    * POST /auth/login
    */
    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'UserName' => ['required'],
            'Password' => ['required'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        $userName = Validator::sanitizeString($data['UserName']);
        $password = $data['Password'];

        // Buscar usuario
        $user = $this->userModel->findByUserName($userName);

        if (!$user) {
            Logger::warning('Falla en el inicio de sesión: usuario no encontrado', [
                'username' => $userName,
            ]);
            Response::unauthorized('Credenciales inválidas');
            return;
        }

        // Verificar password
        if (!$this->userModel->verifyPassword($password, $user['Password'])) {
            Logger::warning('Falla en el inicio de sesión: password incorrecta', [
                'username' => $userName,
            ]);
            Response::unauthorized('Credenciales inválidas');
            return;
        }

        // Generar tokens
        $payload = [
            'idUsuario'     => $user['idUsuario'],
            'idTipoUsuario' => $user['idTipoUsuario'],
            'UserName'      => $user['UserName'],
            'Nombre'        => $user['Nombre'],
        ];

        $accessToken  = $this->jwtService->generateToken($payload, false);
        $refreshToken = $this->jwtService->generateToken($payload, true);

        Logger::info('Usuario iniciado exitosamente', [
            'user_id'  => $user['idUsuario'],
            'username' => $userName,
        ]);

        Response::success([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'user'          => array_diff_key($user, array_flip(['Password'])),
        ], 'Login exitoso');
    }

    /*
    * ===================================================================
    * Renovar token
    * POST /auth/refresh
    */
    public function refresh(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['refresh_token'])) {
            Response::error('Refresh token no proporcionado', null, 400);
            return;
        }

        $tokens = $this->jwtService->refreshToken($data['refresh_token']);

        if (!$tokens) {
            Response::unauthorized('Refresh token inválido o expirado');
            return;
        }

        Logger::info('Token renovado exitosamente');

        Response::success($tokens, 'Token renovado exitosamente');
    }

    /*
    * ===================================================================
    * Registro de usuario
    * POST /auth/register
    */
    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'UserName'      => ['required', 'min' => 3, 'max' => 255],
            'Password'      => ['required', 'min' => 6],
            'Nombre'        => ['required', 'max' => 255],
            'ApellidoPat'   => ['required', 'max' => 255],
            'idTipoUsuario' => ['required', 'integer'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        // Validar unicidad de UserName
        if (!$this->userModel->validateUniqueUserName($data['UserName'])) {
            Response::validationError([
                'UserName' => ['El nombre de usuario ya está en uso'],
            ]);
            return;
        }

        // Validar unicidad de Nombre + ApellidoPat
        if (!$this->userModel->validateUniqueFullName($data['Nombre'], $data['ApellidoPat'])) {
            Response::validationError([
                'Nombre' => ['Ya existe un usuario con ese nombre y apellido'],
            ]);
            return;
        }

        try {
            // Hash password
            $data['Password'] = $this->userModel->hashPassword($data['Password']);

            // Crear usuario
            $userId = $this->userModel->create($data);

            Logger::info('Usuario registrado exitosamente', [
                'user_id'  => $userId,
                'username' => $data['UserName'],
            ]);

            Response::success([
                'idUsuario' => $userId,
            ], 'Usuario registrado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al registrar usuario');
        }
    }
}
