<?php
/*
* ===================================================================
* DetencionOFDetalle Model
*/

namespace App\Models;

class DetencionOFDetalle extends BaseModel {
    protected $table = 'detenciones_of_detalle';
    protected $primaryKey = 'idDetalles';
    protected $fillable = [
        'idDetencionOF',
        'idDetencion',
        'idTiempos',
        'Fecha',
        'HoraInicio',
        'HoraTermino',
        'Minutos',
        'Observaciones',
    ];

    /*
    * ===================================================================
    * Valida unicidad de DetencionOF + Tiempos + Fecha + HoraInicio + HoraTermino
    */
    public function validateUniqueDetalle(int $idDetencionOF, int $idTiempos, string $fecha, string $horaInicio, string $horaTermino, ?int $excludeId = null): bool {
        return !$this->exists([
            'idDetencionOF' => $idDetencionOF,
            'idTiempos'     => $idTiempos,
            'Fecha'         => $fecha,
            'HoraInicio'    => $horaInicio,
            'HoraTermino'   => $horaTermino,
        ], $excludeId);
    }

    /*
    * ===================================================================
    * Obtiene detalles por OF
    */
    public function getByOF(int $idDetencionOF): array {
        $sql = "SELECT dod.*,
                       t.Nombre as TiempoNombre,
                       tt.Nombre as TipoTiempoNombre,
                       tf.Nombre as FamiliaTiempoNombre
                FROM {$this->table} dod
                LEFT JOIN sistema_tiempos_listado t  ON dod.idTiempos      = t.idTiempos
                LEFT JOIN sistema_tiempos_tipos tt   ON t.idTiemposTipo    = tt.idTiemposTipo
                LEFT JOIN sistema_tiempos_familia tf ON t.idTiemposFamilia = tf.idTiemposFamilia
                WHERE dod.idDetencionOF = :idDetencionOF
                ORDER BY dod.Fecha ASC, dod.HoraInicio ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idDetencionOF' => $idDetencionOF]);
        return $stmt->fetchAll();
    }

    /*
    * ===================================================================
    * Obtiene resumen por detención
    */
    public function getResumenByDetencion(int $idDetencion): array {
        $sql = "SELECT 
                    dof.Lote,
                    tt.Nombre as TipoTiempo,
                    tf.Nombre as FamiliaTiempo,
                    SUM(dod.Minutos) as TotalMinutos
                FROM {$this->table} dod
                INNER JOIN detenciones_of dof         ON dod.idDetencionOF  = dof.idDetencionOF
                INNER JOIN sistema_tiempos_listado t  ON dod.idTiempos      = t.idTiempos
                INNER JOIN sistema_tiempos_tipos tt   ON t.idTiemposTipo    = tt.idTiemposTipo
                INNER JOIN sistema_tiempos_familia tf ON t.idTiemposFamilia = tf.idTiemposFamilia
                WHERE dod.idDetencion = :idDetencion
                GROUP BY dof.Lote, tt.Nombre, tf.Nombre
                ORDER BY dof.Lote, tt.Nombre, tf.Nombre";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idDetencion' => $idDetencion]);
        return $stmt->fetchAll();
    }

    /*
    * ===================================================================
    * Obtiene porcentajes por detención
    */
    public function getPorcentajesByDetencion(int $idDetencion): array {
        $sql = "SELECT 
                    tt.Nombre as TipoTiempo,
                    tf.Nombre as FamiliaTiempo,
                    SUM(dod.Minutos) as TotalMinutos,
                    ROUND((SUM(dod.Minutos) * 100.0 / (SELECT SUM(Minutos) FROM {$this->table} WHERE idDetencion = :idDetencion)), 2) as Porcentaje
                FROM {$this->table} dod
                INNER JOIN sistema_tiempos_listado t  ON dod.idTiempos      = t.idTiempos
                INNER JOIN sistema_tiempos_tipos tt   ON t.idTiemposTipo    = tt.idTiemposTipo
                INNER JOIN sistema_tiempos_familia tf ON t.idTiemposFamilia = tf.idTiemposFamilia
                WHERE dod.idDetencion = :idDetencion
                GROUP BY tt.Nombre, tf.Nombre
                ORDER BY TotalMinutos DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idDetencion' => $idDetencion]);
        return $stmt->fetchAll();
    }
}
