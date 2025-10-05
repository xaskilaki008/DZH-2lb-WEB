<?php

class TestModel extends Model {
    protected static $repository = null;
    
    function __construct() {
        parent::__construct();
        // Закомментируйте или удалите эту строку, если класса ResultsVerification нет
        // $this->validator = new ResultsVerification();
    }
    
    protected static function getRepository() {
        return null;
    }
    
    // Остальной ваш существующий код оставляем без изменений
    public $post_data;
    public $result_data;
    public $correct_data = [
        'q1' => '3',
        'q2' => '2',
        'q3' => '30'
    ];

    function checkTest($post_data) {
        $this->post_data = $post_data;
        $this->result_data = [];
        $correct_count = 0;

        foreach ($this->correct_data as $key => $value) {
            $user_answer = $this->post_data[$key] ?? '';
            $is_correct = ($user_answer == $value);
            
            if ($is_correct) {
                $correct_count++;
            }

            $this->result_data[$key] = [
                'user_answer' => $user_answer,
                'correct_answer' => $value,
                'is_correct' => $is_correct
            ];
        }

        $this->result_data['correct_count'] = $correct_count;
        $this->result_data['total_count'] = count($this->correct_data);
        $this->result_data['percent'] = round(($correct_count / count($this->correct_data)) * 100, 2);

        return $this->result_data;
    }

    function get_data() {
        return $this->result_data;
    }
}