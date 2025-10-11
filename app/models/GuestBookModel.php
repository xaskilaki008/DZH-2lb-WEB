<?php
// В GuestBookModel.php - добавить класс FileStorage ПЕРЕД классом GuestBookModel

class FileStorage {
    private $filename = 'posts.csv';

    public function getAll() {
        if (!file_exists($this->filename)) {
            return [];
        }
        
        $reviews = [];
        $file = fopen($this->filename, 'r');
        while (($row = fgetcsv($file, 1000, ';')) !== FALSE) {
            if (count($row) >= 5) {
                // Преобразуем в формат похожий на БД для единообразия
                $reviews[] = [
                    'id' => $row[0],
                    'full_name' => $row[1],
                    'email' => $row[2],
                    'created_at' => $row[3],
                    'message' => $row[4]
                ];
            }
        }
        fclose($file);
        
        return array_reverse($reviews);
    }

    public function save($data) {
        // Генерация ID
        $allData = $this->getAll();
        $nextId = empty($allData) ? 1 : (int)$allData[0]['id'] + 1;
        
        // Подготовка данных для файла
        $rowData = [
            $nextId,
            $data['fullname'] ?? '',
            $data['email'] ?? '',
            date('Y-m-d H:i:s'),
            $data['review'] ?? ''
        ];
        
        // Запись в файл
        $file = fopen($this->filename, 'a');
        fputcsv($file, $rowData, ';');
        fclose($file);
        
        return $nextId;
    }

    public function findBy($field, $value) {
        $allData = $this->getAll();
        $result = [];
        
        foreach ($allData as $row) {
            if (isset($row[$field]) && $row[$field] == $value) {
                $result[] = $row;
            }
        }
        
        return $result;
    }

    public function delete($id) {
        $allData = $this->getAll();
        $newData = [];
        
        foreach ($allData as $row) {
            if ($row['id'] != $id) {
                $newData[] = $row;
            }
        }
        
        // Перезаписываем файл
        $file = fopen($this->filename, 'w');
        foreach ($newData as $row) {
            fputcsv($file, array_values($row), ';');
        }
        fclose($file);
        
        return true;
    }
} // ← ЗАКРЫВАЮЩАЯ СКОБКА для FileStorage

class GuestBookModel extends Model {
    private $dbStorage;
    private $fileStorage;
    private $useDatabase = true;
    
    function __construct() {
        parent::__construct();
        $this->dbStorage = new DatabaseStorage();
        $this->fileStorage = new FileStorage();
    }
    
    public function getAllReviews() {
        if ($this->useDatabase) {
            try {
                $reviews = $this->dbStorage->findAll();
                return $this->convertToOldFormat($reviews);
            } catch (Exception $e) {
                $this->useDatabase = false;
                $reviews = $this->fileStorage->getAll();
                return $this->convertToOldFormat($reviews);
            }
        }
        $reviews = $this->fileStorage->getAll();
        return $this->convertToOldFormat($reviews);
    }
    
    public function addReview($data) {
        // Подготавливаем данные для БД (правильные названия полей из таблицы)
        $dbData = [
            'fullname' => $data['fullname'] ?? '',     // поле fullname
            'email' => $data['email'] ?? '',           // поле email
            'review' => $data['review'] ?? '',         // поле review
            'date' => date('Y-m-d H:i:s')              // поле date
        ];
        
        // Сохраняем в файл
        $fileResult = $this->fileStorage->save($data);
        
        // Сохраняем в БД
        if ($this->useDatabase) {
            try {
                $dbResult = $this->dbStorage->save($dbData);
                error_log("Successfully saved to DB: " . print_r($dbResult, true));
                return ['db' => $dbResult, 'file' => $fileResult];
            } catch (Exception $e) {
                $this->useDatabase = false;
                error_log("Database save error: " . $e->getMessage());
                return ['file' => $fileResult, 'db_error' => $e->getMessage()];
            }
        }
        
        return ['file' => $fileResult];
    }
    
    private function convertToOldFormat($reviews) {
        $oldFormat = [];
        foreach ($reviews as $review) {
            // Преобразуем ассоциативный массив в простой (старый формат)
            $oldFormat[] = [
                $review['fullname'] ?? '',
                $review['email'] ?? '',
                $review['date'] ?? '',
                $review['review'] ?? ''
            ];
        }
        return $oldFormat;
    }
    
    public function useFileOnly() {
        $this->useDatabase = false;
    }
    
    public function parseReviews() {
        return $this->getAllReviews();
    }
}

// Класс для работы с БД (использует существующий BaseActiveRecord)
class DatabaseStorage extends BaseActiveRecord {
    protected static $tablename = 'guestbook';
    
    public function __construct() {
        parent::__construct();
        // Отладка структуры таблицы
        error_log("=== GUESTBOOK TABLE STRUCTURE ===");
        error_log("Fields: " . print_r(static::$dbfields, true));
    }
}