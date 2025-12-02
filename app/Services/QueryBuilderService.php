<?php

namespace OrionERP\Services;

class QueryBuilderService
{
    private $select = [];
    private $from = '';
    private $where = [];
    private $joins = [];
    private $orderBy = [];
    private $limit = null;
    private $params = [];

    public function select(array $fields): self
    {
        $this->select = $fields;
        return $this;
    }

    public function from(string $table, string $alias = ''): self
    {
        $this->from = $alias ? "$table $alias" : $table;
        return $this;
    }

    public function where(string $condition, $value = null): self
    {
        if ($value !== null) {
            $this->where[] = $condition;
            $this->params[] = $value;
        } else {
            $this->where[] = $condition;
        }
        return $this;
    }

    public function join(string $table, string $condition, string $type = 'INNER'): self
    {
        $this->joins[] = "$type JOIN $table ON $condition";
        return $this;
    }

    public function orderBy(string $field, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "$field $direction";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function build(): array
    {
        $sql = 'SELECT ' . implode(', ', $this->select);
        $sql .= ' FROM ' . $this->from;

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        return ['sql' => $sql, 'params' => $this->params];
    }
}

