<?php
/*
* ===================================================================
* Supervisor Controller
*/

namespace App\Controllers;

use App\Models\Supervisor;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class SupervisorController {
    private $supervisorModel;

    public function __construct() {
        $this->supervisorModel = new Supervisor();
    }

    /*
    * ===================================================================
    * Listar todos
    * GET /supervisor
    */
    public function index(): void {
        try {
            $data = $this->supervisorModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener registros');
        }
    }

    /*
    * ===================================================================
    * Obtener uno
    * GET /supervisor/{id}
    */
    public function show(int $id): void {
        try {
            $data = $this->supervisorModel->find($id);

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
    * ===================================================================
    * Crear
    * POST /supervisor
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
        if (!$this->supervisorModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $id = $this->supervisorModel->create($data);
            Response::success(['id' => $id], 'Registro creado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear registro');
        }
    }

    /*
    * ===================================================================
    * Actualizar
    * PUT /supervisor/{id}
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
        if (!$this->supervisorModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $result = $this->supervisorModel->update($id, $data);

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
    * ===================================================================
    * Eliminar (desactivar)
    * DELETE /supervisor/{id}
    */
    public function destroy(int $id): void {
        try {
            $result = $this->supervisorModel->delete($id);

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
    * ===================================================================
    * Activar
    * POST /supervisor/{id}/activate
    */
    public function activate(int $id): void {
        try {
            $result = $this->supervisorModel->activate($id);

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
