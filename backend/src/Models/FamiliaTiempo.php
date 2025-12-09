<?php
/*
* ===================================================================
* FamiliaTiempo Model
*/

namespace App\Models;

class FamiliaTiempo extends BaseModel {
    protected $table      = 'sistema_tiempos_familia';
    protected $primaryKey = 'idTiemposFamilia';
    protected $fillable   = ['Nombre', 'Activo'];

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
