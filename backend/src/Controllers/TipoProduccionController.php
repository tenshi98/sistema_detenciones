<?php
/*
* TipoProduccion Controller
*/

namespace App\Controllers;

use App\Models\TipoProduccion;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class TipoProduccionController {
    private $tipoProduccionModel;

    public function __construct() {
        $this->tipoProduccionModel = new TipoProduccion();
    }

    /*
    * Listar todos
    * GET /tipoProduccion
    */
    public function index(): void {
        try {
            $data = $this->tipoProduccionModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener registros');
        }
    }

    /*
    * Obtener uno
    * GET /tipoProduccion/{id}
    */
    public function show(int $id): void {
        try {
            $data = $this->tipoProduccionModel->find($id);

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
    * POST /tipoProduccion
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
        if (!$this->tipoProduccionModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $id = $this->tipoProduccionModel->create($data);
            Response::success(['id' => $id], 'Registro creado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear registro');
        }
    }

    /*
    * Actualizar
    * PUT /tipoProduccion/{id}
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
        if (!$this->tipoProduccionModel->validateUniqueNombre($data['Nombre'], $id ?? null)) {
            Response::validationError([
                'Nombre' => ['Nombre ya existe'],
            ]);
            return;
        }

        try {
            $result = $this->tipoProduccionModel->update($id, $data);

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
    * DELETE /tipoProduccion/{id}
    */
    public function destroy(int $id): void {
        try {
            $result = $this->tipoProduccionModel->delete($id);

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
    * POST /tipoProduccion/{id}/activate
    */
    public function activate(int $id): void {
        try {
            $result = $this->tipoProduccionModel->activate($id);

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
