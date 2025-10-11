<?php

class GuestBookController extends Controller {
    function __construct() {
        $this->model = new GuestBookModel();
        $this->view = new View();
    }

    function indexAction() {    
        // ЗАМЕНИТЬ parseReviews() на getAllReviews()
        $reviews = $this->model->getAllReviews();    
        $vars = [ 'reviews' => $reviews ];    
        $this->view->render('GuestBookView.php', 'Гостевая книга', $vars);
    }

    function createAction() {
    if (!empty($_POST)) {
        $this->model->validate($_POST);
        $errors = $this->model->validator->getErrors();

        if (empty($errors)) {
            // Если нажата кнопка "Не работает загрузка?"
            if (isset($_POST['file_only'])) {
                $this->model->useFileOnly();
            }
            
            // Подготавливаем данные для сохранения
            $newReview = [
                'fullname' => $_POST['fullname'],
                'email' => $_POST['Email'], 
                'review' => $_POST['review']
            ];
            
            $result = $this->model->addReview($newReview);
            $_POST = array();
        }

        $reviews = $this->model->getAllReviews();
        $vars = [ 'errors' => $errors, 'reviews' => $reviews ];
        $this->view->render('GuestBookView.php', 'Гостевая книга', $vars);
    } else {
        // Показываем форму с существующими отзывами
        $reviews = $this->model->getAllReviews();
        $vars = [ 'reviews' => $reviews ];
        $this->view->render('GuestBookView.php', 'Гостевая книга', $vars);
    }
}
}