<?php

class EditBlogModel extends Model {
    public function __construct() {
        parent::__construct();
        static::$tablename = 'blog';
        static::$dbfields = array('title', 'image', 'text', 'date');
    }

    public function getPosts($get_array) {
        $countOfPosts = $this->getCount();
        $rowsPerPage = 3;
        $totalPages = ceil($countOfPosts / $rowsPerPage);

        if (isset($get_array['page']) && is_numeric($get_array['page'])) {
            $currentPage = (int) $get_array['page'];
        } else {
            $currentPage = 1;
        }

        if ((int) $currentPage > (int) $totalPages) {
            $currentPage = $totalPages;
        }

        if ((int) $currentPage < 1) {
            $currentPage = 1;
        }

        $offset = ($currentPage - 1) * $rowsPerPage;

        $posts = $this->findByPage($offset, $rowsPerPage);

        $result = [
           "posts" => $posts,
           "current_page" => $currentPage, 
           "total_pages" => $totalPages 
        ];

        return $result;
    }

    public function addPost($post_array, $files_array) {
        if ($files_array["file"]["size"] > 0) {
            $timestamp = time(); // Более простая альтернатива
            $image_path = $this->saveImageInFolder($files_array, $timestamp);
            $data = [
                "title" => $post_array["title"],
                "image" => $image_path,
                "text" => $post_array["message"],
                "date" => date('Y-m-d H:i:s')
            ];
        } else {
            $data = [
                "title" => $post_array["title"],
                "image" => NULL,
                "text" => $post_array["message"],
                "date" => date('Y-m-d H:i:s')
            ];
        }

        $this->save($data);
    }

    private function saveImageInFolder($files_array, $timestamp) {
        // Создаем папку uploads если ее нет
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Генерируем имя файла
        $original_name = basename($files_array['file']['name']);
        $new_filename = $timestamp . '_' . $original_name;
        $target_file = $upload_dir . $new_filename;

        // Перемещаем файл
        if (move_uploaded_file($files_array['file']['tmp_name'], $target_file)) {
            return 'uploads/' . $new_filename; // Возвращаем относительный путь для БД
        } else {
            throw new Exception("Ошибка загрузки файла: " . $files_array['file']['error']);
        }
    }
}