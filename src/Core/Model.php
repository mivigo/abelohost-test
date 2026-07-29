<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected array $attributes = [];
    protected array $original = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill attributes into the model.
     */
    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Magic getter for attributes.
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic setter for attributes.
     */
    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Magic isset check.
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Get table name linked to the model.
     */
    public static function getTable(): string
    {
        if (isset(static::$table)) {
            return static::$table;
        }
        
        $parts = explode('\\', static::class);
        $className = end($parts);
        // Snake case pluralize
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
    }

    /**
     * Create model instance populated with database row.
     */
    public static function hydrate(array $attributes): static
    {
        $model = new static();
        $model->attributes = $attributes;
        $model->original = $attributes;
        return $model;
    }

    /**
     * Create query builder instance, pre-filtered for non-deleted records by default.
     */
    public static function query(): QueryBuilder
    {
        $qb = new QueryBuilder(static::class, static::getTable());
        $qb->where('deleted_at', 'IS', 'NULL');
        return $qb;
    }

    /**
     * Get all records from table.
     */
    public static function all(): array
    {
        return static::query()->get();
    }

    /**
     * Find single record by primary key.
     */
    public static function find(int $id): ?static
    {
        return static::query()->where(static::$primaryKey, '=', $id)->first();
    }

    /**
     * Begin query builder with a where condition.
     */
    public static function where(string $column, string $operator, mixed $value = null): QueryBuilder
    {
        return static::query()->where($column, $operator, $value);
    }

    /**
     * Save the model to database (handles INSERT and UPDATE).
     */
    public function save(): bool
    {
        $db = Database::getConnection();
        $table = static::getTable();
        $pk = static::$primaryKey;

        $isNew = !isset($this->attributes[$pk]);
        $now = date('Y-m-d H:i:s');

        if ($isNew) {
            if (!isset($this->attributes['created_at'])) {
                $this->attributes['created_at'] = $now;
            }
            if (!isset($this->attributes['updated_at'])) {
                $this->attributes['updated_at'] = $now;
            }

            $columns = array_keys($this->attributes);
            $placeholders = array_map(fn($col) => ":{$col}", $columns);

            $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($this->attributes);

            if ($result) {
                $this->attributes[$pk] = (int)$db->lastInsertId();
                $this->original = $this->attributes;
                return true;
            }
            return false;
        } else {
            $this->attributes['updated_at'] = $now;

            $updates = [];
            $bindings = [];
            foreach ($this->attributes as $key => $value) {
                if ($key === $pk) continue;
                $updates[] = "`{$key}` = :{$key}";
                $bindings[$key] = $value;
            }
            $bindings[$pk] = $this->attributes[$pk];

            $sql = "UPDATE `{$table}` SET " . implode(', ', $updates) . " WHERE `{$pk}` = :{$pk}";
            $stmt = $db->prepare($sql);
            return $stmt->execute($bindings);
        }
    }

    /**
     * Soft delete model record.
     */
    public function delete(): bool
    {
        $pk = static::$primaryKey;
        if (!isset($this->attributes[$pk])) {
            return false;
        }

        $db = Database::getConnection();
        $table = static::getTable();
        $now = date('Y-m-d H:i:s');

        $this->attributes['deleted_at'] = $now;
        
        $sql = "UPDATE `{$table}` SET `deleted_at` = :deleted_at WHERE `{$pk}` = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'deleted_at' => $now,
            'id' => $this->attributes[$pk]
        ]);
    }

    /**
     * Hard delete model record from database.
     */
    public function forceDelete(): bool
    {
        $pk = static::$primaryKey;
        if (!isset($this->attributes[$pk])) {
            return false;
        }

        $db = Database::getConnection();
        $table = static::getTable();

        $sql = "DELETE FROM `{$table}` WHERE `{$pk}` = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => $this->attributes[$pk]]);
    }
}
