<?php

class DatabaseRepository implements DataRepository
{
    private PDO $db;
    private string $table;
    
    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }
    
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    
    public function find(string $field, $value): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
    
    public function save(array $data): bool
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
    
    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public static function createFromData(array $data): self
    {
        throw new Exception("Use constructor for DatabaseRepository");
    }
}