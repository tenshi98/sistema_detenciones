<?php
/*
* Linea Model
*/

namespace App\Models;

class Linea extends BaseModel {
    protected $table      = 'sistema_lineas';
    protected $primaryKey = 'idLinea';
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
