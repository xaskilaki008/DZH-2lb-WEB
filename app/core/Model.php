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
    
    // Метод для установки данных
    public function setAttributes($data) {
        $this->attributes = $data;
    }
    
    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }
    
    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }
    
    public function toArray() {
        return $this->attributes;
    }

    /** 
     * @return static[]
     */
    public static function all() {
        $items = static::getRepository()->all();
        $models = [];
        
        foreach ($items as $item) {
            $model = new static();
            $model->setAttributes($item);
            $models[] = $model;
        }
        
        return $models;
    }

    /** 
     * @return static[]
     */
    public static function findBy($criteria) {
        $models = [];
        
        foreach ($criteria as $field => $value) {
            $items = static::getRepository()->find($field, $value);
            
            foreach ($items as $item) {
                $model = new static();
                $model->setAttributes($item);
                $models[] = $model;
            }
        }
        
        return $models;
    }

    // Исправленный метод для совместимости с BaseActiveRecord
    public function save($data = null) {
        if ($data === null) {
            $data = $this->attributes;
        }
        static::getRepository()->save($data);
    }

    public static function create($data) {
        $model = new static();
        $model->setAttributes($data);
        $model->save();
        return $model;
    }

    public function delete() {
        if (isset($this->attributes['id'])) {
            static::getRepository()->delete($this->attributes['id']);
        }
    }
    
    // Старые методы для обратной совместимости
    public function get_data() { 
        return [];
    }
    
    public function validate($post_data) {
        $this->validator->validate($post_data);
    }
    
    abstract protected static function getRepository();
}