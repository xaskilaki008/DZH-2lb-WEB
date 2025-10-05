<?php

class FormValidation {
    public $errors = [];
    public $rules = [
        'fullname' => [
			'isNotEmpty'
		],
        'group' => [
			'isNotEmpty'
		],
		'phone' => [
			'isNotEmpty',
			'isPhone'
		],
		'date' => [
			'isNotEmpty'
		],
		'Email' => [
			'isEmail'
        ],
        'message' => [
            'isNotEmpty'
        ],
        'review' => [
            'isNotEmpty'
        ],
        'title' => [
            'isNotEmpty'
        ]
    ];

    public function setRule($field_name, $validator_name) {
        // Проверяем, существует ли уже такой ключ в массиве правил
        if (!isset($this->rules[$field_name])) {
            $this->rules[$field_name] = [];
        }
        array_push($this->rules[$field_name], $validator_name);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function validate($post_array) {
        foreach ($post_array as $key => $item) {
            // Добавляем проверку существования правила для этого ключа
            if (isset($this->rules[$key]) && $this->rules[$key]) {
                foreach ($this->rules[$key] as $rule) {
                    $this->$rule($item, $key);
                }
            }
        }
    }

    public function isNotEmptySelect($data, $key) {
		if ($data == 'Выберите ответ') {
			array_push($this->errors, "Поле Вопрос 2 не должно быть пустым");
		}
		return true;
    }

    public function isNotEmpty($data, $key) {
		if (empty($data)) {
            if ($key == "fullname") {
                $resultKey = "ФИО";
            } elseif ($key == "group") {
                $resultKey = "Группа";
            } elseif ($key == "message") {
                $resultKey = "Сообщение";
            } elseif ($key == "title") {
                $resultKey = "Тема";
            } elseif ($key == "review") {
                $resultKey = "Отзыв";
            } elseif ($key == "phone") {
                $resultKey = "Телефон";
            } elseif ($key == "date") {
                $resultKey = "Дата";
            } elseif ($key == "q3") {
                $resultKey = "Вопрос 3";
            }
			array_push($this->errors, "Поле $resultKey не должно быть пустым");
		}
		return true;
    }
    
    public function isInteger($data, $key = null, $value = null) {
		if (ctype_digit($data)) {
			array_push($this->errors, "Поле Вопрос 3 содержит числа");
		}
		return false;
    }
    
    public function isLess($data, $key, $value = null){
		if ($this->isInteger($data) && ((int)$this->isInteger($data) >= $value)) {
			array_push($this->errors, "Поле $key слишком длинное");
		}
		return true;
	}

	public function isGreater($data, $key, $value = null){
		if ($this->isInteger($data) && ((int)$this->isInteger($data) <= $value)) {
			array_push($this->errors, "Поле $key слишком короткое");
		}
		return true;
    }
    
    public function isEmail($data, $key, $value = null) {
    // Более простое и надежное регулярное выражение для email
    if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    
    // Альтернативная проверка для кириллических email
    if (preg_match('/^[a-zA-Z0-9а-яА-Я._%+-]+@[a-zA-Z0-9а-яА-Я.-]+\.[a-zA-Zа-яА-Я]{2,}$/u', $data)) {
        return true;
    }
    
    array_push($this->errors, "В поле $key неверно введена почта");
    return false;
    }

	public function isPhone($data, $key, $value = null) {
		if (preg_match('/^(\+7|\+3)([0-9]{8,10})$/', $data)) {
			return true;
		}
		array_push($this->errors, "В поле Телефон неверно введен номер телефона");
	}
}