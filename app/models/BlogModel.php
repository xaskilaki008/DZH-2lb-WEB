<?php

class BlogModel extends Model {
    protected static ?DataRepository $repository = null;
    
    protected static function getRepository(): DataRepository {
        if (self::$repository === null) {
            // Для файлов
            $columns = ['id', 'title', 'content', 'author', 'created_at'];
            self::$repository = new FileRepository('posts.csv', ',', $columns);
            
            // Или для базы данных (раскомментируйте если нужно):
            // $db = new PDO("mysql:dbname=web2;host=localhost;charset=utf8", "root", "");
            // self::$repository = new DatabaseRepository($db, 'posts');
        }
        return self::$repository;
    }
}