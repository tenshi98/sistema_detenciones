<?php
/*
* RutaProduccion Model
*/

namespace App\Models;

class RutaProduccion extends BaseModel {
    protected $table      = 'sistema_rutas_produccion';
    protected $primaryKey = 'idRutaProduccion';
    protected $fillable   = ['idMaterial', 'idMaterialRuta', 'Nombre', 'VelNominal', 'Activo'];

    /*
    * Valida unicidad de idMaterial + idMaterialRuta + Nombre
    */
    public function validateUniqueidMaterialidMaterialRutaNombre(string $idMaterial, string $idMaterialRuta, string $nombre, ?int $excludeId = null): bool {
        return !$this->exists([
            'idMaterial' => $idMaterial, 'idMaterialRuta' => $idMaterialRuta, 'Nombre' => $nombre,
        ], $excludeId);
    }

}
