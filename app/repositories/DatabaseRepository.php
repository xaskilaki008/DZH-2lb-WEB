<?php

class DatabaseRepository implements DataRepository
{
    private $db;
    private $table;
    
    public function __construct($db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }
    
    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    
    public function find($field, $value)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    
    public function save($data)
    {
        if (isset($data['id'])) {
            $fields = [];
            $values = [];
            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $fields[] = "{$key} = ?";
                    $values[] = $value;
                }
            }
            $values[] = $data['id'];
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        } else {
            $fields = array_keys($data);
            $placeholders = str_repeat('?,', count($fields) - 1) . '?';
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";
            $values = array_values($data);
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public static function createFromData($data)
    {
        throw new Exception("Use constructor for DatabaseRepository");
    }
}