<?php
/*
* ===================================================================
* EstadoDetencion Controller
*/

namespace App\Controllers;

use App\Models\EstadoDetencion;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class EstadoDetencionController {
    private $estadoDetencionModel;

    public function __construct() {
        $this->estadoDetencionModel = new EstadoDetencion();
    }

    /*
    * ===================================================================
    * Listar todos
    * GET /detencionesEstados
    */
    public function index(): void {
        try {
            $data = $this->estadoDetencionModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener registros');
        }
    }

    /*
    * ===================================================================
    * Obtener uno
    * GET /detencionesEstados/{id}
    */
    public function show(int $id): void {
        try {
            $data = $this->estadoDetencionModel->find($id);

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
    * POST /detencionesEstados
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
        if (!$this->estadoDetencionModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $id = $this->estadoDetencionModel->create($data);
            Response::success(['id' => $id], 'Registro creado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear registro');
        }
    }

    /*
    * ===================================================================
    * Actualizar
    * PUT /detencionesEstados/{id}
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
        if (!$this->estadoDetencionModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $result = $this->estadoDetencionModel->update($id, $data);

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
    * DELETE /detencionesEstados/{id}
    */
    public function destroy(int $id): void {
        try {
            $result = $this->estadoDetencionModel->delete($id);

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
    * POST /detencionesEstados/{id}/activate
    */
    public function activate(int $id): void {
        try {
            $result = $this->estadoDetencionModel->activate($id);

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
