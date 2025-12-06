<?php
/*
* TipoProduccion Model
*/

namespace App\Models;

class TipoProduccion extends BaseModel {
    protected $table      = 'sistema_tipo_produccion';
    protected $primaryKey = 'idTipoProduccion';
    protected $fillable   = ['Nombre', 'Activo'];

    /*
    * Valida unicidad de Nombre
    */
    public function validateUniqueNombre(string $nombre, ?int $excludeId = null): bool {
        return !$this->exists([
            'Nombre' => $nombre,
        ], $excludeId);
    }

}
