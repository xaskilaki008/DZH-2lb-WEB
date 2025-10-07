<?php

require 'app/models/validators/FormValidation.php';
require_once 'app/core/BaseActiveRecord.php';

abstract class Model extends BaseActiveRecord {
    public $validator;
    protected array $attributes = [];
    protected static $repository = null;
    
    function __construct() {
        parent::__construct();
        $this->validator = new FormValidation();
        $this->attributes = [];
    }
    
    // Метод для установки данных
    public function setAttributes(array $data): void {
        $this->attributes = $data;
    }
    
    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }
    
    public function __set($name, $value): void {
        $this->attributes[$name] = $value;
    }
    
    public function toArray(): array {
        return $this->attributes;
    }

    /** 
     * @return static[]
     */
    public static function all(): array {
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
    public static function findBy(array $criteria): array {
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

    public function save(): void {
        static::getRepository()->save($this->attributes);
    }

    public static function create(array $data): static {
        $model = new static();
        $model->setAttributes($data);
        $model->save();
        return $model;
    }

    public function delete(): void {
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