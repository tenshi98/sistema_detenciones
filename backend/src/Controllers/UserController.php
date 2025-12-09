<?php
/*
* ===================================================================
* User Controller
*/

namespace App\Controllers;

use App\Models\User;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /*
    * ===================================================================
    * Listar todos
    * GET /users
    */
    public function index(): void {
        try {
            $users = $this->userModel->all();

            // Ocultar passwords
            $users = array_map(function ($user) {
                unset($user['Password']);
                return $user;
            }, $users);

            Response::success($users);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener usuarios');
        }
    }

    /*
    * ===================================================================
    * Obtener uno
    * GET /users/{id}
    */
    public function show(int $id): void {
        try {
            $user = $this->userModel->find($id);

            if (!$user) {
                Response::notFound('Usuario no encontrado');
                return;
            }

            unset($user['Password']);
            Response::success($user);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener usuario');
        }
    }

    /*
    * ===================================================================
    * Crear
    * POST /users
    */
    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'UserName'      => ['required', 'min' => 3],
            'Password'      => ['required', 'min' => 6],
            'Nombre'        => ['required'],
            'ApellidoPat'   => ['required'],
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

            $id = $this->userModel->create($data);
            Response::success(['id' => $id], 'Usuario creado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear usuario');
        }
    }

    /*
    * ===================================================================
    * Actualizar
    * PUT /users/{id}
    */
    public function update(int $id): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'UserName'      => ['required', 'min' => 3],
            'Nombre'        => ['required'],
            'ApellidoPat'   => ['required'],
            'idTipoUsuario' => ['required', 'integer'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        // Validar unicidad de UserName
        if (!$this->userModel->validateUniqueUserName($data['UserName'], $id)) {
            Response::validationError([
                'UserName' => ['El nombre de usuario ya está en uso'],
            ]);
            return;
        }

        // Validar unicidad de Nombre + ApellidoPat
        if (!$this->userModel->validateUniqueFullName($data['Nombre'], $data['ApellidoPat'], $id)) {
            Response::validationError([
                'Nombre' => ['Ya existe un usuario con ese nombre y apellido'],
            ]);
            return;
        }

        try {
            // Si se proporciona nueva password, hashearla
            if (isset($data['Password']) && !empty($data['Password'])) {
                $data['Password'] = $this->userModel->hashPassword($data['Password']);
            } else {
                unset($data['Password']);
            }

            $result = $this->userModel->update($id, $data);

            if (!$result) {
                Response::notFound('Usuario no encontrado');
                return;
            }

            Response::success(null, 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al actualizar usuario');
        }
    }

    /*
    * ===================================================================
    * Eliminar (desactivar)
    * DELETE /users/{id}
    */
    public function destroy(int $id): void {
        try {
            $result = $this->userModel->delete($id);

            if (!$result) {
                Response::notFound('Usuario no encontrado');
                return;
            }

            Response::success(null, 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al eliminar usuario');
        }
    }

    /*
    * ===================================================================
    * Activar
    * POST /users/{id}/activate
    */
    public function activate(int $id): void {
        try {
            $result = $this->userModel->activate($id);

            if (!$result) {
                Response::notFound('Usuario no encontrado');
                return;
            }

            Response::success(null, 'Usuario activado exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al activar usuario');
        }
    }
}
