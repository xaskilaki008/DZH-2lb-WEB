<?php

class FileRepository implements DataRepository
{
    private string $filePath;
    private string $delimiter;
    private array $columns;
    
    public function __construct(string $filePath, string $delimiter = ';', array $columns = [])
    {
        $this->filePath = $filePath;
        $this->delimiter = $delimiter;
        $this->columns = $columns;
        
        if (!file_exists($filePath)) {
            touch($filePath);
        }
    }
    
    public function all(): array
    {
        $items = [];
        
        if (($handle = fopen($this->filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, $this->delimiter)) !== false) {
                if (!empty(array_filter($data))) {
                    $items[] = $this->mapToAssociativeArray($data);
                }
            }
            fclose($handle);
        }
        
        return $items;
    }
    
    public function find(string $field, $value): array
    {
        $allItems = $this->all();
        $results = [];
        
        foreach ($allItems as $item) {
            if (isset($item[$field]) && $item[$field] == $value) {
                $results[] = $item;
            }
        }
        
        return $results;
    }
    
    public function save(array $data): bool
    {
        if (!isset($data['id'])) {
            $allItems = $this->all();
            $maxId = 0;
            foreach ($allItems as $item) {
                if (isset($item['id']) && $item['id'] > $maxId) {
                    $maxId = (int)$item['id'];
                }
            }
            $data['id'] = $maxId + 1;
        }
        
        $allItems = $this->all();
        $found = false;
        
        foreach ($allItems as $index => $item) {
            if (isset($item['id']) && $item['id'] == $data['id']) {
                $allItems[$index] = $data;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $allItems[] = $data;
        }
        
        return $this->writeAll($allItems);
    }
    
    public function delete($id): bool
    {
        $allItems = $this->all();
        $newItems = [];
        $deleted = false;
        
        foreach ($allItems as $item) {
            if (!isset($item['id']) || $item['id'] != $id) {
                $newItems[] = $item;
            } else {
                $deleted = true;
            }
        }
        
        if ($deleted) {
            return $this->writeAll($newItems);
        }
        
        return false;
    }
    
    public static function createFromData(array $data): self
    {
        return new self(
            $data['filePath'] ?? '',
            $data['delimiter'] ?? ';',
            $data['columns'] ?? []
        );
    }
    
    private function mapToAssociativeArray(array $row): array
    {
        if (empty($this->columns)) {
            return array_combine(range(0, count($row) - 1), $row);
        }
        
        $result = [];
        foreach ($this->columns as $index => $column) {
            $result[$column] = $row[$index] ?? null;
        }
        return $result;
    }
    
    private function writeAll(array $items): bool
    {
        $tempFile = $this->filePath . '.tmp';
        
        if (($handle = fopen($tempFile, 'w')) !== false) {
            foreach ($items as $item) {
                fputcsv($handle, array_values($item), $this->delimiter);
            }
            fclose($handle);
            
            if (rename($tempFile, $this->filePath)) {
                return true;
            }
        }
        
        return false;
    }
}