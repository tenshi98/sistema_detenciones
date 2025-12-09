<?php
/*
* ===================================================================
* Export Controller
*
* Controlador para exportar reportes
*/

namespace App\Controllers;

use App\Models\Detencion;
use App\Models\DetencionOF;
use App\Models\DetencionOFDetalle;
use App\Utils\Response;
use App\Utils\Logger;

class ExportController {
    private $detencionModel;
    private $detencionOFModel;
    private $detalleModel;

    public function __construct(){
        $this->detencionModel   = new Detencion();
        $this->detencionOFModel = new DetencionOF();
        $this->detalleModel     = new DetencionOFDetalle();
    }

    /*
    * ===================================================================
    * Exportar detención completa
    * GET /export/detencion/{id}
    */
    public function exportDetencion(int $id): void {
        try {
            $detencion = $this->detencionModel->findWithRelations($id);

            if (!$detencion) {
                Response::notFound('Detención no encontrada');
                return;
            }

            $ofs         = $this->detencionOFModel->getByDetencion($id);
            $resumen     = $this->detalleModel->getResumenByDetencion($id);
            $porcentajes = $this->detalleModel->getPorcentajesByDetencion($id);

            $data = [
                'detencion'   => $detencion,
                'ofs'         => $ofs,
                'resumen'     => $resumen,
                'porcentajes' => $porcentajes,
            ];

            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al exportar detención');
        }
    }

    /*
    * ===================================================================
    * Exportar listado de detenciones
    * GET /export/detenciones
    */
    public function exportDetenciones(): void {
        try {
            $detenciones = $this->detencionModel->all();

            // Preparar datos para exportación
            $data = array_map(function($det) {
                return [
                    'ID'              => $det['idDetencion'],
                    'Fecha'           => $det['Fecha'],
                    'Línea'           => $det['idLinea'],
                    'Turno'           => $det['idTurnos'],
                    'Supervisor'      => $det['idSupervisor'],
                    'Tipo Producción' => $det['idTipoProduccion'],
                    'Estado'          => $det['idEstado'],
                    'Observaciones'   => $det['Observaciones'] ?? '',
                ];
            }, $detenciones);

            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al exportar detenciones');
        }
    }

    /*
    * ===================================================================
    * Exportar resumen general
    * GET /export/resumen
    */
    public function exportResumenGeneral(): void {
        try {
            $sql = "SELECT
            detenciones.Fecha,
            sistema_lineas.Nombre as Linea,
            sistema_turnos.Nombre as Turno,
            COUNT(DISTINCT detenciones_of.idDetencionOF) as TotalOFs,
            SUM(detenciones_of_detalle.Minutos) as TotalMinutos
            FROM detenciones d
            LEFT JOIN sistema_lineas          ON detenciones.idLinea          = sistema_lineas.idLinea
            LEFT JOIN sistema_turnos t        ON detenciones.idTurnos         = sistema_turnos.idTurnos
            LEFT JOIN detenciones_of          ON detenciones.idDetencion      = detenciones_of.idDetencion
            LEFT JOIN detenciones_of_detalle  ON detenciones_of.idDetencionOF = detenciones_of_detalle.idDetencionOF
            WHERE detenciones.Activo = 1
            GROUP BY detenciones.Fecha, sistema_lineas.Nombre, sistema_turnos.Nombre
            ORDER BY detenciones.Fecha DESC";

            $stmt = $this->detencionModel->db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();

            Response::success($data);
        } catch (\Exception $e) {
            Logger::exception($e);
            Response::serverError('Error al exportar resumen general');
        }
    }
}
