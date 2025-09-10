<?php

require 'app/models/validators/ResultsVerification.php';

class TestModel extends Model {
    public $validator;

    public function __construct() {
        $this->validator = new ResultsVerification();
        static::$tablename = 'test';
        // Указываем правильные имена полей согласно структуре БД
        static::$dbfields = array('full_name', 'q1', 'q2', 'q3', 'checkbox1', 'checkbox2', 'checkbox3', 'is_correct', 'created_at');
    }

    public function createTest($post_array) {
        // Используем проверку isset для каждого поля
        $checkbox1 = isset($post_array["checkbox1"]) ? 1 : 0; // преобразуем в integer
        $checkbox2 = isset($post_array["checkbox2"]) ? 1 : 0;
        $checkbox3 = isset($post_array["checkbox3"]) ? 1 : 0;
        
        $result = $this->validator->getResult();

        $data = [
            "full_name" => $post_array["fullname"] ?? '', // используем оператор null coalescing
            "q1" => $post_array["q1"] ?? '', // добавляем проверку на существование
            "q2" => $post_array["q2"] ?? '',
            "q3" => $post_array["q3"] ?? '',
            "checkbox1" => $checkbox1, // integer значение (1 или 0)
            "checkbox2" => $checkbox2,
            "checkbox3" => $checkbox3,
            "is_correct" => $result,
            "created_at" => date('Y-m-d H:i:s')
        ];

        $this->save($data);
    }
}