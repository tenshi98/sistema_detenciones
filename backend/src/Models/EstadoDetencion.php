<?php
/*
* ===================================================================
* EstadoDetencion Model
*/

namespace App\Models;

class EstadoDetencion extends BaseModel {
    protected $table      = 'sistema_estado_detencion';
    protected $primaryKey = 'idEstado';
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
