<?php
/*
* ===================================================================
* Base Model
*
* Clase base para todos los modelos con funcionalidad CRUD genérica
*/

namespace App\Models;

use App\Config\Database;
use App\Utils\Logger;
use PDO;

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /*
    * ===================================================================
    * Obtiene todos los registros activos
    *
    * @param array $conditions Condiciones WHERE adicionales
    * @return array
    */
    public function all(array $conditions = []): array {
        try {
            $where  = "Activo = 1";
            $params = [];

            if (!empty($conditions)) {
                foreach ($conditions as $key => $value) {
                    $where .= " AND {$key} = :{$key}";
                    $params[":{$key}"] = $value;
                }
            }

            $sql  = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$this->primaryKey} ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            Logger::error("Error fetching all from {$this->table}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /*
    * ===================================================================
    * Encuentra un registro por ID
    *
    * @param int $id
    * @return array|null
    */
    public function find(int $id): ?array {
        try {
            $sql  = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);

            $result = $stmt->fetch();
            return $result ?: null;
        } catch (\PDOException $e) {
            Logger::error("Error finding record in {$this->table}", [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /*
    * ===================================================================
    * Crea un nuevo registro
    *
    * @param array $data
    * @return int ID del registro creado
    */
    public function create(array $data): int {
        try {
            // Filtrar solo campos permitidos
            $data         = $this->filterFillable($data);
            $fields       = array_keys($data);
            $placeholders = array_map(fn($field) => ":{$field}", $fields);
            $sql          = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt         = $this->db->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $stmt->execute();
            $id = (int) $this->db->lastInsertId();

            Logger::info("Record created in {$this->table}", [
                'id'   => $id,
                'data' => $data,
            ]);

            return $id;
        } catch (\PDOException $e) {
            Logger::error("Error creating record in {$this->table}", [
                'data'  => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /*
    * ===================================================================
    * Actualiza un registro
    *
    * @param int $id
    * @param array $data
    * @return bool
    */
    public function update(int $id, array $data): bool {
        try {
            // Filtrar solo campos permitidos
            $data = $this->filterFillable($data);

            $setClause = [];
            foreach (array_keys($data) as $field) {
                $setClause[] = "{$field} = :{$field}";
            }

            $sql  = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            $result = $stmt->execute();

            Logger::info("Record updated in {$this->table}", [
                'id'   => $id,
                'data' => $data,
            ]);

            return $result;
        } catch (\PDOException $e) {
            Logger::error("Error updating record in {$this->table}", [
                'id'    => $id,
                'data'  => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /*
    * ===================================================================
    * Elimina (desactiva) un registro
    *
    * @param int $id
    * @return bool
    */
    public function delete(int $id): bool {
        try {
            $sql    = "UPDATE {$this->table} SET Activo = 0 WHERE {$this->primaryKey} = :id";
            $stmt   = $this->db->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            Logger::info("Record soft deleted in {$this->table}", [
                'id' => $id,
            ]);

            return $result;
        } catch (\PDOException $e) {
            Logger::error("Error deleting record in {$this->table}", [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /*
    * ===================================================================
    * Activa un registro
    *
    * @param int $id
    * @return bool
    */
    public function activate(int $id): bool {
        try {
            $sql    = "UPDATE {$this->table} SET Activo = 1 WHERE {$this->primaryKey} = :id";
            $stmt   = $this->db->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            Logger::info("Record activated in {$this->table}", [
                'id' => $id,
            ]);

            return $result;
        } catch (\PDOException $e) {
            Logger::error("Error activating record in {$this->table}", [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /*
    * ===================================================================
    * Verifica si existe un registro con ciertos criterios
    *
    * @param array $conditions
    * @param int|null $excludeId ID a excluir de la búsqueda
    * @return bool
    */
    public function exists(array $conditions, ?int $excludeId = null): bool {
        try {
            $where  = [];
            $params = [];

            foreach ($conditions as $key => $value) {
                $where[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }

            if ($excludeId !== null) {
                $where[] = "{$this->primaryKey} != :excludeId";
                $params[':excludeId'] = $excludeId;
            }

            $sql  = "SELECT COUNT(*) as count FROM {$this->table} WHERE " . implode(' AND ', $where);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (\PDOException $e) {
            Logger::error("Error checking existence in {$this->table}", [
                'conditions' => $conditions,
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /*
    * ===================================================================
    * Filtra los datos para incluir solo campos permitidos
    *
    * @param array $data
    * @return array
    */
    protected function filterFillable(array $data): array {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /*
    * ===================================================================
    * Oculta campos sensibles de un registro
    *
    * @param array $record
    * @return array
    */
    protected function hideFields(array $record): array {
        foreach ($this->hidden as $field) {
            unset($record[$field]);
        }
        return $record;
    }
}
