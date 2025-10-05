<?php

class BlogModel extends Model {
    protected static $repository = null;
    
    protected static function getRepository() {
        if (self::$repository === null) {
            // Для файлов
            $columns = ['id', 'title', 'content', 'author', 'created_at'];
            self::$repository = new FileRepository('posts.csv', ',', $columns);
        }
        return self::$repository;
    }
    
    // ДОБАВЬТЕ ЭТОТ МЕТОД ДЛЯ ОБРАТНОЙ СОВМЕСТИМОСТИ
    public function getPosts() {
        // Используем новый метод all() для получения постов
        return self::all();
    }
    
    // ДОБАВЬТЕ ЭТОТ МЕТОД ЕСЛИ НУЖНО
    public function addPost($data) {
        return self::create($data);
    }
}