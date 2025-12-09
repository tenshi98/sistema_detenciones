<?php
/*
* ===================================================================
* Turno Controller
*/

namespace App\Controllers;

use App\Models\Turno;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class TurnoController {
    private $turnoModel;

    public function __construct() {
        $this->turnoModel = new Turno();
    }

    /*
    * ===================================================================
    * Listar todos
    * GET /turnos
    */
    public function index(): void {
        try {
            $data = $this->turnoModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener registros');
        }
    }

    /*
    * ===================================================================
    * Obtener uno
    * GET /turnos/{id}
    */
    public function show(int $id): void {
        try {
            $data = $this->turnoModel->find($id);

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
    * POST /turnos
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
        if (!$this->turnoModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $id = $this->turnoModel->create($data);
            Response::success(['id' => $id], 'Registro creado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear registro');
        }
    }

    /*
    * ===================================================================
    * Actualizar
    * PUT /turnos/{id}
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
        if (!$this->turnoModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $result = $this->turnoModel->update($id, $data);

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
    * DELETE /turnos/{id}
    */
    public function destroy(int $id): void {
        try {
            $result = $this->turnoModel->delete($id);

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
    * POST /turnos/{id}/activate
    */
    public function activate(int $id): void {
        try {
            $result = $this->turnoModel->activate($id);

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
