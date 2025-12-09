<?php
/*
* ===================================================================
* Detencion Model
*/

namespace App\Models;

class Detencion extends BaseModel {
    protected $table = 'detenciones';
    protected $primaryKey = 'idDetencion';
    protected $fillable = [
        'idUsuario',
        'idLinea',
        'idTurnos',
        'idSupervisor',
        'idTipoProduccion',
        'Fecha',
        'idEstado',
        'Observaciones',
    ];

    /*
    * ===================================================================
    * Valida unicidad de Linea + Turno + TipoProduccion + Fecha
    */
    public function validateUniqueDetencion(int $idLinea, int $idTurnos, int $idTipoProduccion, string $fecha, ?int $excludeId = null): bool {
        return !$this->exists([
            'idLinea'          => $idLinea,
            'idTurnos'         => $idTurnos,
            'idTipoProduccion' => $idTipoProduccion,
            'Fecha'            => $fecha,
        ], $excludeId);
    }

    /*
    * ===================================================================
    * Obtiene detenciones abiertas
    */
    public function getAbiertas(): array {
        $sql = "SELECT d.*, 
                       l.Nombre as LineaNombre,
                       t.Nombre as TurnoNombre,
                       s.Nombre as SupervisorNombre,
                       tp.Nombre as TipoProduccionNombre,
                       u.UserName as UsuarioNombre,
                       e.Nombre as EstadoNombre
                FROM {$this->table} d
                LEFT JOIN sistema_lineas l           ON d.idLinea          = l.idLinea
                LEFT JOIN sistema_turnos t           ON d.idTurnos         = t.idTurnos
                LEFT JOIN sistema_supervisores s     ON d.idSupervisor     = s.idSupervisor
                LEFT JOIN sistema_tipo_produccion tp ON d.idTipoProduccion = tp.idTipoProduccion
                LEFT JOIN usuarios_listado u         ON d.idUsuario        = u.idUsuario
                LEFT JOIN sistema_estado_detencion e ON d.idEstado         = e.idEstado
                WHERE d.idEstado = 1
                ORDER BY d.Fecha DESC, d.idDetencion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*
    * ===================================================================
    * Obtiene detención con relaciones
    */
    public function findWithRelations(int $id): ?array {
        $sql = "SELECT d.*, 
                       l.Nombre as LineaNombre,
                       t.Nombre as TurnoNombre,
                       s.Nombre as SupervisorNombre,
                       tp.Nombre as TipoProduccionNombre,
                       u.UserName as UsuarioNombre,
                       e.Nombre as EstadoNombre
                FROM {$this->table} d
                LEFT JOIN sistema_lineas l           ON d.idLinea          = l.idLinea
                LEFT JOIN sistema_turnos t           ON d.idTurnos         = t.idTurnos
                LEFT JOIN sistema_supervisores s     ON d.idSupervisor     = s.idSupervisor
                LEFT JOIN sistema_tipo_produccion tp ON d.idTipoProduccion = tp.idTipoProduccion
                LEFT JOIN usuarios_listado u         ON d.idUsuario        = u.idUsuario
                LEFT JOIN sistema_estado_detencion e ON d.idEstado         = e.idEstado
                WHERE d.idDetencion = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
