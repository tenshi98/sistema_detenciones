<?php
/*
* Tiempo Model
*/

namespace App\Models;

class Tiempo extends BaseModel {
    protected $table      = 'sistema_tiempos_listado';
    protected $primaryKey = 'idTiempos';
    protected $fillable   = ['idTiemposTipo', 'idTiemposFamilia', 'Nombre', 'Activo'];

    /*
    * Valida unicidad de idTiemposTipo + idTiemposFamilia + Nombre
    */
    public function validateUniqueidTiemposTipoidTiemposFamiliaNombre(string $idTiemposTipo, string $idTiemposFamilia, string $nombre, ?int $excludeId = null): bool {
        return !$this->exists([
            'idTiemposTipo' => $idTiemposTipo, 'idTiemposFamilia' => $idTiemposFamilia, 'Nombre' => $nombre,
        ], $excludeId);
    }

}
