<?php

class BaseActiveRecord
{
    public static $pdo;
    protected static $tablename;
    protected static $dbfields = array();

    public function __construct()
    {
        if (!static::$tablename) {
            return;
        }
        static::setupConnection();
        static::getFields();
    }

    public static function getFields()
    {
        $stmt = static::$pdo->query("SHOW FIELDS FROM " . static::$tablename);
        static::$dbfields = []; // очищаем массив
        while ($row = $stmt->fetch()) {
            static::$dbfields[] = $row['Field']; // сохраняем только имена полей
        }
    }

    public static function setupConnection()
    {
        if (!isset(static::$pdo)) {
            try {
                static::$pdo = new PDO("mysql:dbname=web2; host=localhost; char-set=utf8", "root", "");
            } catch (PDOException $ex) {
                die("Ошибка подключения к БД: $ex");
            }
        }
    }

    public static function find($id)
    {
        $sql = "SELECT * FROM " . static::$tablename . " WHERE id=$id";
        $stmt = static::$pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $ar_obj = new static();
        foreach ($row as $key => $value) {
            $ar_obj->$key = $value;
        }

        return $ar_obj;
    }

    public static function findAll()
    {
        static::setupConnection();
        static::getFields();

        $result = [];
        $sql = "SELECT * FROM " . static::$tablename;
        $stmt = static::$pdo->query($sql);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row);
        }

        return $result;
    }

    public static function findByPage($offset, $rowsPerPage)
    {
        static::setupConnection();

        $result = [];
        $sql = "SELECT * FROM " . static::$tablename . " ORDER BY date DESC LIMIT " . $offset . ", " . $rowsPerPage;
        $stmt = static::$pdo->query($sql);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($result, $row);
        }

        return $result;
    }

    public static function getCount()
    {
        static::setupConnection();

        $sql = "SELECT COUNT(*) FROM " . static::$tablename;
        $stmt = static::$pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return current($result);
    }

    public function save($data)
    {
        static::setupConnection();

        // ДЕБАГ: посмотрим что у нас в свойствах
        error_log("Table: " . static::$tablename);
        error_log("DB fields: " . print_r(static::$dbfields, true));
        error_log("Data: " . print_r($data, true));

        $fields = implode("`, `", static::$dbfields);
        $fields = '`' . $fields . '`';

        // Создаем плейсхолдеры для подготовленного запроса
        $placeholders = implode(', ', array_fill(0, count(static::$dbfields), '?'));

        $tablename = static::$tablename;
        $sql = "INSERT INTO $tablename ($fields) VALUES ($placeholders)";

        $stmt = static::$pdo->prepare($sql);

        // Преобразуем ассоциативный массив в простой (в порядке dbfields)
        $values = [];
        foreach (static::$dbfields as $field) {
            $values[] = $data[$field] ?? null;
        }

        if ($stmt->execute($values)) {
            return static::$pdo->lastInsertId();
        } else {
            throw new Exception('Database error: ' . implode(' ', $stmt->errorInfo()));
        }
    }

    public function delete()
    {
        $sql = "DELETE FROM " . static::$tablename . " WHERE ID=" . $this->id;
        $stmt = static::$pdo->query($sql);

        if ($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            print_r(static::$pdo->errorInfo());
        }
    }

    // Добавить в класс BaseActiveRecord
    public static function findBy($field, $value)
    {
        // Универсальный поиск по полю и значению
        static::setupConnection();

        $sql = "SELECT * FROM " . static::$tablename . " WHERE $field = ?";
        $stmt = static::$pdo->prepare($sql);
        $stmt->execute([$value]);

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }
}
class FileStorage {
    private $filename = 'posts.csv';

    public function getAll() {
        // чтение из CSV файла
    }

    public function findBy($field, $value) {
        // поиск в CSV по полю и значению
    }

    public function save($data) {
        // сохранение в CSV с генерацией ID
    }

    public function delete($id) {
        // удаление из CSV по ID
    }
}