<?php
namespace Donations\Core;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = db();
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function all($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $sql .= " WHERE " . $this->buildWhereClause($conditions, $params);
        }

        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }

        if ($limit) {
            $sql .= " LIMIT " . $limit;
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function create($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';

        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") 
                VALUES ($placeholders)";

        $this->db->execute($sql, $values);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $set = implode('=?,', $fields) . '=?';
        $values[] = $id;

        $sql = "UPDATE {$this->table} SET $set 
                WHERE {$this->primaryKey} = ?";

        return $this->db->execute($sql, $values);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }

    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $sql .= " WHERE " . $this->buildWhereClause($conditions, $params);
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }

    protected function buildWhereClause($conditions, &$params) {
        $clauses = [];
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $operator = $value[0];
                $val = $value[1];
            } else {
                $operator = '=';
                $val = $value;
            }
            $clauses[] = "$field $operator ?";
            $params[] = $val;
        }
        return implode(' AND ', $clauses);
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollback() {
        return $this->db->rollback();
    }
} 