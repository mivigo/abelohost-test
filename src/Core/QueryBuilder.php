<?php

namespace App\Core;

use PDO;

class QueryBuilder
{
    protected array $wheres = [];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function __construct(
        protected string $modelClass,
        protected string $table
    ) {}

    /**
     * Add a where condition.
     */
    public function where(string $column, string $operator, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    /**
     * Add an order by clause.
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = [
            'column' => $column,
            'direction' => strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC'
        ];
        return $this;
    }

    /**
     * Set query limit.
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set query offset.
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get all results.
     */
    public function get(): array
    {
        $db = Database::getConnection();
        
        $bindings = [];
        $sql = "SELECT * FROM `{$this->table}`" . $this->compileWheres($bindings);

        if (!empty($this->orders)) {
            $sql .= " ORDER BY ";
            $orderParts = [];
            foreach ($this->orders as $order) {
                $orderParts[] = "`{$order['column']}` {$order['direction']}";
            }
            $sql .= implode(', ', $orderParts);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($bindings);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];
        foreach ($rows as $row) {
            $models[] = ($this->modelClass)::hydrate($row);
        }

        return $models;
    }

    /**
     * Get the first result or null.
     */
    public function first(): ?object
    {
        $results = $this->limit(1)->get();
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Get count of matching records.
     */
    public function count(): int
    {
        $db = Database::getConnection();
        $bindings = [];
        $sql = "SELECT COUNT(*) FROM `{$this->table}`" . $this->compileWheres($bindings);

        $stmt = $db->prepare($sql);
        $stmt->execute($bindings);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Compile the where conditions into SQL string and append parameters to bindings array.
     */
    protected function compileWheres(array &$bindings): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        $conditions = [];
        foreach ($this->wheres as $index => $where) {
            $paramName = "where_{$where['column']}_{$index}";
            if ($where['operator'] === 'IS' || $where['operator'] === 'IS NOT') {
                $conditions[] = "`{$where['column']}` {$where['operator']} {$where['value']}";
            } else {
                $conditions[] = "`{$where['column']}` {$where['operator']} :{$paramName}";
                $bindings[$paramName] = $where['value'];
            }
        }

        return " WHERE " . implode(' AND ', $conditions);
    }
}
