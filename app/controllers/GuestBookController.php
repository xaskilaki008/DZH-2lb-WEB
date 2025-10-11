<?php

class GuestBookController extends Controller {
    function __construct() {
		$this->model = new GuestBookModel();
		$this->view = new View();
    }

	function indexAction() {	
        $reviews = $this->model->parseReviews();	
        $vars = [ 'reviews' => $reviews ];	
		$this->view->render('GuestBookView.php', 'Гостевая книга', $vars);
    }

    function createAction() {
        if (!empty($_POST)) {
            $this->model->validate($_POST);
            $errors = $this->model->validator->getErrors();

            if (empty($errors)) {
                // Использование новой абстракции
                $newReview = [
                    'author' => $_POST['fullname'],
                    'email' => $_POST['Email'],
                    'date' => date('Y-m-d H:i:s'),
                    'text' => $_POST['review']
                ];

                $this->model->addReview($newReview);
                $_POST = array();
            }

            $reviews = $this->model->getAllReviews(); // ← новый метод
            $vars = [ 'errors' => $errors, 'reviews' => $reviews ];
            $this->view->render('GuestBookView.php', 'Гостевая книга', $vars);
        }
    }
}