<?php
/*
* ===================================================================
* Detencion Controller
*/

namespace App\Controllers;

use App\Models\Detencion;
use App\Middleware\JWTMiddleware;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class DetencionController {
    // Variables
    private $detencionModel;

    public function __construct() {
        $this->detencionModel = new Detencion();
    }

    /*
    * ===================================================================
    * Listar todas las detenciones
    * GET /detenciones
    */
    public function index(): void {
        try {
            $data = $this->detencionModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener detenciones');
        }
    }

    /*
    * ===================================================================
    * Listar detenciones abiertas
    * GET /detenciones/abiertas
    */
    public function abiertas(): void {
        try {
            $data = $this->detencionModel->getAbiertas();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener detenciones abiertas');
        }
    }

    /*
    * ===================================================================
    * Obtener una detención
    * GET /detenciones/{id}
    */
    public function show(int $id): void {
        try {
            $data = $this->detencionModel->findWithRelations($id);

            if (!$data) {
                Response::notFound('Detención no encontrada');
                return;
            }

            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener detención');
        }
    }

    /*
    * ===================================================================
    * Crear detención
    * POST /detenciones
    */
    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Obtener usuario actual
        $currentUser       = JWTMiddleware::getCurrentUser();
        $data['idUsuario'] = $currentUser->idUsuario;
        $data['idEstado']  = 1; // Abierta por defecto

        // Validar datos
        $errors = Validator::validate($data, [
            'idLinea'          => ['required', 'integer'],
            'idTurnos'         => ['required', 'integer'],
            'idSupervisor'     => ['required', 'integer'],
            'idTipoProduccion' => ['required', 'integer'],
            'Fecha'            => ['required', 'date'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        // Validar unicidad
        if (!$this->detencionModel->validateUniqueDetencion(
            $data['idLinea'],
            $data['idTurnos'],
            $data['idTipoProduccion'],
            $data['Fecha']
        )) {
            Response::validationError([
                'Fecha' => ['Ya existe una detención para esta línea, turno, tipo de producción y fecha'],
            ]);
            return;
        }

        try {
            $id = $this->detencionModel->create($data);
            Response::success(['id' => $id], 'Detención creada exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear detención');
        }
    }

    /*
    * ===================================================================
    * Actualizar detención
    * PUT /detenciones/{id}
    */
    public function update(int $id): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos
        $errors = Validator::validate($data, [
            'idLinea'          => ['required', 'integer'],
            'idTurnos'         => ['required', 'integer'],
            'idSupervisor'     => ['required', 'integer'],
            'idTipoProduccion' => ['required', 'integer'],
            'Fecha'            => ['required', 'date'],
            'idEstado'         => ['required', 'integer'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        // Validar unicidad
        if (!$this->detencionModel->validateUniqueDetencion(
            $data['idLinea'],
            $data['idTurnos'],
            $data['idTipoProduccion'],
            $data['Fecha'],
            $id
        )) {
            Response::validationError([
                'Fecha' => ['Ya existe una detención para esta línea, turno, tipo de producción y fecha'],
            ]);
            return;
        }

        try {
            $result = $this->detencionModel->update($id, $data);

            if (!$result) {
                Response::notFound('Detención no encontrada');
                return;
            }

            Response::success(null, 'Detención actualizada exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al actualizar detención');
        }
    }
}
