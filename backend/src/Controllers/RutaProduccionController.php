<?php
/*
* ===================================================================
* RutaProduccion Controller
*/

namespace App\Controllers;

use App\Models\RutaProduccion;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class RutaProduccionController {
    private $rutaProduccionModel;

    public function __construct() {
        $this->rutaProduccionModel = new RutaProduccion();
    }

    /*
    * ===================================================================
    * Listar todos
    * GET /rutaProduccion
    */
    public function index(): void {
        try {
            $data = $this->rutaProduccionModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener registros');
        }
    }

    /*
    * ===================================================================
    * Obtener uno
    * GET /rutaProduccion/{id}
    */
    public function show(int $id): void {
        try {
            $data = $this->rutaProduccionModel->find($id);

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
    * POST /rutaProduccion
    */
    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'idMaterial'     => ['required'],
            'idMaterialRuta' => ['required'],
            'Nombre'         => ['required'],
            'VelNominal'     => ['required']
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        // Validar unicidad de idMaterial + idMaterialRuta + Nombre
        if (!$this->rutaProduccionModel->validateUniqueidMaterialidMaterialRutaNombre($data['idMaterial'], $data['idMaterialRuta'], $data['Nombre'], $id ?? null)) {
            Response::validationError([
                'idMaterial' => ['idMaterial + idMaterialRuta + Nombre ya existe'],
            ]);
            return;
        }

        try {
            $id = $this->rutaProduccionModel->create($data);
            Response::success(['id' => $id], 'Registro creado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear registro');
        }
    }

    /*
    * ===================================================================
    * Actualizar
    * PUT /rutaProduccion/{id}
    */
    public function update(int $id): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'idMaterial'     => ['required'],
            'idMaterialRuta' => ['required'],
            'Nombre'         => ['required'],
            'VelNominal'     => ['required']
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        // Validar unicidad de idMaterial + idMaterialRuta + Nombre
        if (!$this->rutaProduccionModel->validateUniqueidMaterialidMaterialRutaNombre($data['idMaterial'], $data['idMaterialRuta'], $data['Nombre'], $id ?? null)) {
            Response::validationError([
                'idMaterial' => ['idMaterial + idMaterialRuta + Nombre ya existe'],
            ]);
            return;
        }

        try {
            $result = $this->rutaProduccionModel->update($id, $data);

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
    * DELETE /rutaProduccion/{id}
    */
    public function destroy(int $id): void {
        try {
            $result = $this->rutaProduccionModel->delete($id);

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
    * POST /rutaProduccion/{id}/activate
    */
    public function activate(int $id): void {
        try {
            $result = $this->rutaProduccionModel->activate($id);

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
