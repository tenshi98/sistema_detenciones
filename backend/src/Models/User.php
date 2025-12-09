<?php
/*
* ===================================================================
* User Model
*/

namespace App\Models;

class User extends BaseModel {
    protected $table = 'usuarios_listado';
    protected $primaryKey = 'idUsuario';
    protected $fillable = [
        'idTipoUsuario',
        'UserName',
        'Password',
        'Nombre',
        'ApellidoPat',
        'ApellidoMat',
        'Activo',
    ];
    protected $hidden = ['Password'];

    /*
    * ===================================================================
    * Valida unicidad de UserName
    */
    public function validateUniqueUserName(string $userName, ?int $excludeId = null): bool {
        return !$this->exists(['UserName' => $userName], $excludeId);
    }

    /*
    * ===================================================================
    * Valida unicidad de Nombre + ApellidoPat
    */
    public function validateUniqueFullName(string $nombre, string $apellidoPat, ?int $excludeId = null): bool {
        return !$this->exists([
            'Nombre'      => $nombre,
            'ApellidoPat' => $apellidoPat,
        ], $excludeId);
    }

    /*
    * ===================================================================
    * Busca usuario por UserName
    */
    public function findByUserName(string $userName): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE UserName = :userName AND Activo = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userName' => $userName]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /*
    * ===================================================================
    * Verifica password
    */
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /*
    * ===================================================================
    * Hash password
    */
    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
