<?php
/*
* ===================================================================
* Material Model
*/

namespace App\Models;

class Material extends BaseModel {
    protected $table      = 'sistema_materiales';
    protected $primaryKey = 'idMaterial';
    protected $fillable   = ['Nombre', 'Descripcion', 'Activo'];

    /*
    * ===================================================================
    * Valida unicidad de Nombre
    */
    public function validateUniqueNombre(string $nombre, ?int $excludeId = null): bool {
        return !$this->exists([
            'Nombre' => $nombre,
        ], $excludeId);
    }

}
