<?php
/*
* Supervisor Model
*/

namespace App\Models;

class Supervisor extends BaseModel {
    protected $table      = 'sistema_supervisores';
    protected $primaryKey = 'idSupervisor';
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
