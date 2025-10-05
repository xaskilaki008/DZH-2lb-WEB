<?php

class Controller {
    public $model;
    public $view;
    
    function __construct() {
        $this->view = new View();
        // УБРАЛИ создание Model, так как он теперь абстрактный
        // Конкретная модель будет создаваться в дочерних контроллерах
        $this->model = null;
    }
    
    function action_index() { }
}