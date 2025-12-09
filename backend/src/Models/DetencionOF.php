<?php
/*
* ===================================================================
* DetencionOF Model
*/

namespace App\Models;

class DetencionOF extends BaseModel {
    protected $table = 'detenciones_of';
    protected $primaryKey = 'idDetencionOF';
    protected $fillable = [
        'idDetencion',
        'idMaterial',
        'idMaterialRuta',
        'idRutaProduccion',
        'CantidadProd',
        'Lote',
        'Observaciones',
    ];

    /*
    * ===================================================================
    * Valida unicidad de Material + MaterialRuta + Lote
    */
    public function validateUniqueOF(int $idMaterial, int $idMaterialRuta, string $lote, ?int $excludeId = null): bool {
        return !$this->exists([
            'idMaterial'     => $idMaterial,
            'idMaterialRuta' => $idMaterialRuta,
            'Lote'           => $lote,
        ], $excludeId);
    }

    /*
    * ===================================================================
    * Obtiene OFs por detención
    */
    public function getByDetencion(int $idDetencion): array {
        $sql = "SELECT dof.*,
                       m.Nombre as MaterialNombre,
                       mr.Nombre as RutaMaterialNombre,
                       rp.Nombre as RutaProduccionNombre,
                       rp.VelNominal
                FROM {$this->table} dof
                LEFT JOIN sistema_materiales m        ON dof.idMaterial       = m.idMaterial
                LEFT JOIN sistema_materiales_rutas mr ON dof.idMaterialRuta   = mr.idMaterialRuta
                LEFT JOIN sistema_rutas_produccion rp ON dof.idRutaProduccion = rp.idRutaProduccion
                WHERE dof.idDetencion = :idDetencion
                ORDER BY dof.idDetencionOF ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idDetencion' => $idDetencion]);
        return $stmt->fetchAll();
    }
}
