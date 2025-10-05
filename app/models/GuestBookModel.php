<?php

require_once 'app/core/DataRepository.php';
require_once 'app/repositories/FileRepository.php';
class HobbyModel extends Model {
    protected static $repository = null;
    
    protected static function getRepository()
    {
        return null;
    }
}
class GuestBookModel extends Model {
    protected static $repository = null;
    
    protected static function getRepository()
    {
        if (self::$repository === null) {
            $columns = ['id', 'fullname', 'email', 'created_at', 'review'];
            self::$repository = new FileRepository('reviews.inc', ';', $columns);
        }
        return self::$repository;
    }
    
    // Старые методы для обратной совместимости
    public function parseReviews()
    {
        $items = self::all();
        $reviews = [];
        
        foreach ($items as $item) {
            $reviews[] = [
                $item->fullname ?? '',
                $item->email ?? '', 
                $item->created_at ?? '',
                $item->review ?? ''
            ];
        }
        
        return array_reverse($reviews);
    }

    public function addReview($data)
    {
        // Создаем модель и устанавливаем атрибуты
        $model = new static();
        $model->setAttributes([
            'fullname' => $data[0] ?? '',
            'email' => $data[1] ?? '',
            'created_at' => $data[2] ?? '',
            'review' => $data[3] ?? ''
        ]);
        $model->save();
    }
}