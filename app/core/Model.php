<?php

require 'app/models/validators/FormValidation.php';
require_once 'app/core/BaseActiveRecord.php';

abstract class Model extends BaseActiveRecord {
    public $validator;
    protected $attributes = [];
    protected static $repository = null;
    
    function __construct() {
        parent::__construct();
        $this->validator = new FormValidation();
        $this->attributes = [];
    }
    
    // Сделаем метод неабстрактным с заглушкой
    protected static function getRepository()
    {
        return null;
    }
    
    // Остальные методы оставляем как были
    public function setAttributes($data) {
        $this->attributes = $data;
    }
    
    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }
    
    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }
    
    public static function all() {
        // Если репозиторий не настроен, возвращаем пустой массив
        if (static::getRepository() === null) {
            return [];
        }
        $items = static::getRepository()->all();
        $models = [];
        
        foreach ($items as $item) {
            $model = new static();
            $model->setAttributes($item);
            $models[] = $model;
        }
        
        return $models;
    }

    // ... остальные методы (findBy, save, create, delete) аналогично добавляем проверку на null

    public function save($data = null) {
        if (static::getRepository() === null) {
            return; // Заглушка для моделей без репозитория
        }
        if ($data === null) {
            $data = $this->attributes;
        }
        static::getRepository()->save($data);
    }

    // Старые методы для обратной совместимости
    public function get_data() { 
        return [];
    }
    
    public function validate($post_data) {
        $this->validator->validate($post_data);
    }
}