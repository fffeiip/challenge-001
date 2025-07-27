<?php
namespace Core;

use Core\Database;
use PDO;
use App\Models\User;

abstract class Model
{
    protected static $table;
    protected static $primaryKey = 'id';
    protected static $softDelete = true;

    /**
     * Get all records, optionally filtered by criteria.
     *
     * @param array $criteria Column-value pairs for filtering.
     * @return array Model instances.
     */
    public static function all(array $criteria = []): array
    {
        $db = Database::getInstance()->pdo();
        $sql = "SELECT * FROM " . static::$table;
        $where = [];
        if (static::$softDelete) {
            $where[] = "deleted_at IS NULL";
        }
        foreach ($criteria as $column => $value) {
            $where[] = "$column = :$column";
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($criteria);
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    /**
     * Find a record by primary key.
     *
     * @param mixed $id The value of the primary key.
     * @return static|null Returns the model instance or null if not found.
     */
    public static function find($id)
    {
        $db = Database::getInstance()->pdo();
        $sql = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id";
        if (static::$softDelete) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchObject(static::class);
    }

    /**
     * Get all records accessible to the user.
     *
     * @param User $user
     * @return array
     */
    public static function allForUser(User $user): array
    {
        if ($user->role === 'super_admin') {
            return static::all();
        }
        return static::all(['id' => $user->store_id]);
    }

    /**
     * Retrieves records based on the user's store association.
     *
     * If the user has the 'super_admin' role, all records are returned.
     * Otherwise, only records associated with the user's store are returned.
     *
     * @param User $user The user whose store association is used for filtering.
     * @return array The list of records matching the criteria.
     */
    public static function findByStore(User $user)
    {
        if ($user->role === 'super_admin') {
            return static::all();
        }
        return static::all(['store_id' => $user->store_id]);
    }

    /**
     * Find a record by ID if accessible by the user.
     *
     * @param mixed $id
     * @param User $user
     * @return static|null
     */
    public static function findForUser($id, User $user)
    {
        $record = static::find($id);
        if (! $record) {
            return null;
        }
        if ($user->role === 'super_admin' || $record->store_id === $user->store_id) {
            return $record;
        }
        return null;
    }

    /**
     * Get records where $column is in $values.
     *
     * @param string $column
     * @param array $values
     * @return array
     */
    public static function whereIn(string $column, array $values): array
    {
        if (empty($values)) {
            return [];
        }

        // build “?, ?, ?” placeholders
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s IN (%s)%s',
            static::$table,
            $column,
            $placeholders,
            static::$softDelete ? ' AND deleted_at IS NULL' : ''
        );

        $stmt = Database::getInstance()->pdo()->prepare($sql);
        $stmt->execute(array_values($values));  // positional bind
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    /**
     * Save the model to the database (insert or update).
     *
     * @return $this Returns the model instance after saving.
     */
    public function save()
    {
        $db = Database::getInstance()->pdo();
        $props = get_object_vars($this);

        if (isset($this->{static::$primaryKey})) {
            // Update existing record
            $sets = array_filter(array_keys($props), function($k) { return $k !== static::$primaryKey; });
            $sql = "UPDATE " . static::$table . " SET "
                . implode(', ', array_map(function($k) { return "$k = :$k"; }, $sets))
                . " WHERE " . static::$primaryKey . " = :" . static::$primaryKey;
        } else {
            // Create new record
            $keys = array_keys($props);
            $sql = "INSERT INTO " . static::$table
                . " (" . implode(', ', $keys) . ") VALUES (:" . implode(', :', $keys) . ")";
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($props);
        if (! isset($this->{static::$primaryKey})) {
            $this->{static::$primaryKey} = (int)$db->lastInsertId();
        }
        return $this;
    }

    /**
     * Delete this record from the database.
     *
     * @param int $deletedBy ID of the user performing the deletion.
     * If soft delete is enabled, this will set the deleted_at and deleted_by fields.
     * @return bool True on success, false otherwise.
     */

    public function delete(int $deletedBy): bool
    {
        if (static::$softDelete) {
            $this->deleted_at = date('Y-m-d H:i:s');
            if ($deletedBy !== null) {
                $this->deleted_by = $deletedBy;
            }
            $this->updated_at = date('Y-m-d H:i:s');
            $this->save();
            return true;
        }
        $db = Database::getInstance()->pdo();
        $stmt = $db->prepare("DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id");
        return $stmt->execute(['id' => $this->{static::$primaryKey}]);
    }

    /**
     * "Belongs to" relationship.
     *
     * @param string $relatedClass Related model class.
     * @param string $foreignKey Foreign key on this model.
     * @param string $ownerKey Primary key on related model.
     * @return object|null
     */
    public function belongsTo(string $relatedClass, string $foreignKey, string $ownerKey = 'id')
    {
        $db = Database::getInstance()->pdo();
        $table = $relatedClass::$table;
        $stmt = $db->prepare("SELECT * FROM $table WHERE $ownerKey = :val");
        $stmt->execute(['val' => $this->{$foreignKey}]);
        return $stmt->fetchObject($relatedClass);
    }
}
