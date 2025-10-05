<?php

class BlogController extends Controller {
    function __construct() {
        $this->model = new BlogModel();
        $this->view = new View();
    }

    function indexAction() {	
        // ЗАМЕНИТЕ эту строку:
        // $posts = $this->model->getPosts();
        
        // НА эту (используя новый метод):
        $posts = BlogModel::all();
        
        $vars = [ 'posts' => $posts ];	
        $this->view->render('BlogView.php', 'Блог', $vars);
    }
}