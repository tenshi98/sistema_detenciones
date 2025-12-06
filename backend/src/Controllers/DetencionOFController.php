<?php
/*
* DetencionOF Controller
*/

namespace App\Controllers;

use App\Models\DetencionOF;
use App\Utils\Response;
use App\Utils\Validator;
use App\Utils\Logger;

class DetencionOFController {
    private $detencionOFModel;

    public function __construct() {
        $this->detencionOFModel = new DetencionOF();
    }

    public function index(): void {
        try {
            $data = $this->detencionOFModel->all();
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener órdenes de fabricación');
        }
    }

    public function byDetencion(int $id): void {
        try {
            $data = $this->detencionOFModel->getByDetencion($id);
            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener órdenes de fabricación');
        }
    }

    public function show(int $id): void {
        try {
            $data = $this->detencionOFModel->find($id);

            if (!$data) {
                Response::notFound('Orden de fabricación no encontrada');
                return;
            }

            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al obtener orden de fabricación');
        }
    }

    public function store(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = Validator::validate($data, [
            'idDetencion'      => ['required', 'integer'],
            'idMaterial'       => ['required', 'integer'],
            'idMaterialRuta'   => ['required', 'integer'],
            'idRutaProduccion' => ['required', 'integer'],
            'CantidadProd'     => ['required', 'integer'],
            'Lote'             => ['required'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        if (!$this->detencionOFModel->validateUniqueOF(
            $data['idMaterial'],
            $data['idMaterialRuta'],
            $data['Lote']
        )) {
            Response::validationError([
                'Lote' => ['Ya existe una OF con este material, ruta y lote'],
            ]);
            return;
        }

        try {
            $id = $this->detencionOFModel->create($data);
            Response::success(['id' => $id], 'Orden de fabricación creada exitosamente', 201);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al crear orden de fabricación');
        }
    }

    public function update(int $id): void {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = Validator::validate($data, [
            'idDetencion'      => ['required', 'integer'],
            'idMaterial'       => ['required', 'integer'],
            'idMaterialRuta'   => ['required', 'integer'],
            'idRutaProduccion' => ['required', 'integer'],
            'CantidadProd'     => ['required', 'integer'],
            'Lote'             => ['required'],
        ]);

        if (!empty($errors)) {
            Response::validationError($errors);
            return;
        }

        if (!$this->detencionOFModel->validateUniqueOF(
            $data['idMaterial'],
            $data['idMaterialRuta'],
            $data['Lote'],
            $id
        )) {
            Response::validationError([
                'Lote' => ['Ya existe una OF con este material, ruta y lote'],
            ]);
            return;
        }

        try {
            $result = $this->detencionOFModel->update($id, $data);

            if (!$result) {
                Response::notFound('Orden de fabricación no encontrada');
                return;
            }

            Response::success(null, 'Orden de fabricación actualizada exitosamente');
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al actualizar orden de fabricación');
        }
    }
}
