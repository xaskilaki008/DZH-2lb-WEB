<?php

class FormValidation {
    public $errors = [];
    public $rules = [
        'fullname' => [
            'isNotEmpty',
            'isValidName'  // ← НОВОЕ ПРАВИЛО
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
            'isNotEmpty',
            'isMinLength',     // ← НОВОЕ ПРАВИЛО (минимум 10 символов)
            'isMeaningfulText' // ← НОВОЕ ПРАВИЛО
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
		if (preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', $data)) {
			return true;
		}
		array_push($this->errors, "В поле $key неверно введена почта");
	}

	public function isPhone($data, $key, $value = null) {
		if (preg_match('/^(\+7|\+3)([0-9]{8,10})$/', $data)) {
			return true;
		}
		array_push($this->errors, "В поле Телефон неверно введен номер телефона");
	}
    // Добавить в класс FormValidation
    public function isValidName($data, $key) {
        // Проверка ФИО: кириллица, 2-3 слова, каждое с заглавной буквы
        if (!preg_match('/^[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+(\s[А-ЯЁ][а-яё]+)?$/u', trim($data))) {
            array_push($this->errors, "ФИО должно содержать 2 или 3 слова на кириллице, каждое с заглавной буквы");
            return false;
        }
        return true;
    }

    public function isMinLength($data, $key, $minLength = 10) {
        // Проверка минимальной длины текста
        if (strlen(trim($data)) < $minLength) {
            $fieldName = $this->getFieldName($key);
            array_push($this->errors, "Поле $fieldName должно содержать минимум $minLength символов");
            return false;
        }
        return true;
    }

    public function isMeaningfulText($data, $key) {
        // Проверка на осмысленность: не только цифры, не только повторяющиеся символы
        $text = trim($data);

        // Проверка на только цифры и знаки препинания
        if (preg_match('/^[0-9\s\.,!?\-_;]+$/', $text)) {
            array_push($this->errors, "Текст отзыва должен содержать буквы, а не только цифры и знаки препинания");
            return false;
        }

        // Проверка на повторяющиеся символы (например, "аааааа")
        if (preg_match('/(.)\1{4,}/', $text)) {
            array_push($this->errors, "Текст содержит слишком много повторяющихся символов");
            return false;
        }

        return true;
    }

    private function getFieldName($key) {
        // Вспомогательный метод для получения русских названий полей
        $names = [
            'fullname' => 'ФИО',
            'review' => 'Отзыв',
            'Email' => 'Email'
        ];
        return $names[$key] ?? $key;
    }
}