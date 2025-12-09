<?php
/*
* ===================================================================
* TipoTiempo Model
*/

namespace App\Models;

class TipoTiempo extends BaseModel {
    protected $table      = 'sistema_tiempos_tipos';
    protected $primaryKey = 'idTiemposTipo';
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
