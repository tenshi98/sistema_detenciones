<?php
/*
* RutaMaterial Model
*/

namespace App\Models;

class RutaMaterial extends BaseModel {
    protected $table      = 'sistema_materiales_rutas';
    protected $primaryKey = 'idMaterialRuta';
    protected $fillable   = ['idMaterial', 'Nombre', 'Activo'];

    /*
    * Valida unicidad de idMaterial + Nombre
    */
    public function validateUniqueidMaterialNombre(string $idMaterial, string $nombre, ?int $excludeId = null): bool {
        return !$this->exists([
            'idMaterial' => $idMaterial, 'Nombre' => $nombre,
        ], $excludeId);
    }

}
