<?php
/*
* UserType Controller
*/

namespace App\Controllers;

use App\Models\UserType;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class UserTypeController {
    private $userTypeModel;

    public function __construct() {
        $this->userTypeModel = new UserType();
    }

    /*
    * Listar todos
    * GET /userTypes
    */
    public function index(): void {
        try {
            $data = $this->userTypeModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener registros');
        }
    }

    /*
    * Obtener uno
    * GET /userTypes/{id}
    */
    public function show(int $id): void {
        try {
            $data = $this->userTypeModel->find($id);

            if (!$data) {
                Response::notFound('Registro no encontrado');
                return;
            }

            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener registro');
        }
    }

    /*
    * Crear
    * POST /userTypes
    */
    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'Nombre' => ['required']
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        // Validar unicidad de Nombre
        if (!$this->userTypeModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $id = $this->userTypeModel->create($data);
            Response::success(['id' => $id], 'Registro creado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear registro');
        }
    }

    /*
    * Actualizar
    * PUT /userTypes/{id}
    */
    public function update(int $id): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'Nombre' => ['required']
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        // Validar unicidad de Nombre
        if (!$this->userTypeModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $result = $this->userTypeModel->update($id, $data);

            if (!$result) {
                Response::notFound('Registro no encontrado');
                return;
            }

            Response::success(null, 'Registro actualizado exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al actualizar registro');
        }
    }

    /*
    * Eliminar (desactivar)
    * DELETE /userTypes/{id}
    */
    public function destroy(int $id): void {
        try {
            $result = $this->userTypeModel->delete($id);

            if (!$result) {
                Response::notFound('Registro no encontrado');
                return;
            }

            Response::success(null, 'Registro eliminado exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al eliminar registro');
        }
    }

    /*
    * Activar
    * POST /userTypes/{id}/activate
    */
    public function activate(int $id): void {
        try {
            $result = $this->userTypeModel->activate($id);

            if (!$result) {
                Response::notFound('Registro no encontrado');
                return;
            }

            Response::success(null, 'Registro activado exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al activar registro');
        }
    }
}
