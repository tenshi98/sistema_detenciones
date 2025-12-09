<?php
/*
* ===================================================================
* DetencionOFDetalle Controller
*/

namespace App\Controllers;

use App\Models\DetencionOFDetalle;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class DetencionOFDetalleController {
    private $detalleModel;

    public function __construct() {
        $this->detalleModel = new DetencionOFDetalle();
    }

    public function index(): void {
        try {
            $data = $this->detalleModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener detalles');
        }
    }

    public function byOF(int $id): void {
        try {
            $data = $this->detalleModel->getByOF($id);
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener detalles');
        }
    }

    public function resumen(int $id): void {
        try {
            $data = $this->detalleModel->getResumenByDetencion($id);
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener resumen');
        }
    }

    public function porcentajes(int $id): void {
        try {
            $data = $this->detalleModel->getPorcentajesByDetencion($id);
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener porcentajes');
        }
    }

    public function show(int $id): void {
        try {
            $data = $this->detalleModel->find($id);

            if (!$data) {
                Response::notFound('Detalle no encontrado');
                return;
            }

            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener detalle');
        }
    }

    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = Validator::validate($data, [
            'idDetencionOF' => ['required', 'integer'],
            'idDetencion'   => ['required', 'integer'],
            'idTiempos'     => ['required', 'integer'],
            'Fecha'         => ['required', 'date'],
            'HoraInicio'    => ['required', 'time'],
            'HoraTermino'   => ['required', 'time'],
            'Minutos'       => ['required', 'integer'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        if (!$this->detalleModel->validateUniqueDetalle(
            $data['idDetencionOF'],
            $data['idTiempos'],
            $data['Fecha'],
            $data['HoraInicio'],
            $data['HoraTermino']
        )) {
            Response::validationError([
                'Fecha' => ['Ya existe un detalle con estos datos'],
            ]);
            return;
        }

        try {
            $id = $this->detalleModel->create($data);
            Response::success(['id' => $id], 'Detalle creado exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear detalle');
        }
    }

    public function update(int $id): void {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = Validator::validate($data, [
            'idDetencionOF' => ['required', 'integer'],
            'idDetencion'   => ['required', 'integer'],
            'idTiempos'     => ['required', 'integer'],
            'Fecha'         => ['required', 'date'],
            'HoraInicio'    => ['required', 'time'],
            'HoraTermino'   => ['required', 'time'],
            'Minutos'       => ['required', 'integer'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        if (!$this->detalleModel->validateUniqueDetalle(
            $data['idDetencionOF'],
            $data['idTiempos'],
            $data['Fecha'],
            $data['HoraInicio'],
            $data['HoraTermino'],
            $id
        )) {
            Response::validationError([
                'Fecha' => ['Ya existe un detalle con estos datos'],
            ]);
            return;
        }

        try {
            $result = $this->detalleModel->update($id, $data);

            if (!$result) {
                Response::notFound('Detalle no encontrado');
                return;
            }

            Response::success(null, 'Detalle actualizado exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al actualizar detalle');
        }
    }

    public function destroy(int $id): void {
        try {
            // Para detalles, hacemos eliminación física
            $sql    = "DELETE FROM detenciones_of_detalle WHERE idDetalles = :id";
            $stmt   = $this->detalleModel->db->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            if (!$result) {
                Response::notFound('Detalle no encontrado');
                return;
            }

            Response::success(null, 'Detalle eliminado exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al eliminar detalle');
        }
    }
}
