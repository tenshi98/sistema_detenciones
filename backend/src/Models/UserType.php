<?php
/*
* UserType Model
*/

namespace App\Models;

class UserType extends BaseModel {
    protected $table      = 'usuarios_tipos';
    protected $primaryKey = 'idTipoUsuario';
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
