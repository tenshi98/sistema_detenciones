<?php
/*
* ===================================================================
* Turno Model
*/

namespace App\Models;

class Turno extends BaseModel {
    protected $table      = 'sistema_turnos';
    protected $primaryKey = 'idTurnos';
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
