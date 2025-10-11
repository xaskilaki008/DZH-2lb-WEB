<?php

class GuestBookModel extends Model {
    private $storage;

    function __construct($storageType = 'file') {
        parent::__construct();
        $this->storage = ($storageType === 'database')
            ? new DatabaseStorage()
            : new FileStorage();
    }

    // Делегирование методов хранилищу
    public function getAllReviews() {
        return $this->storage->getAll();
    }

    public function findReviewsBy($field, $value) {
        return $this->storage->findBy($field, $value);
    }

    public function addReview($data) {
        return $this->storage->save($data);
    }

    public function deleteReview($id) {
        return $this->storage->delete($id);
    }
}


// Класс для работы с БД (использует существующий BaseActiveRecord)
class DatabaseStorage extends BaseActiveRecord {
    protected static $tablename = 'guestbook';
}