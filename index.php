<?php 

require_once 'app/core/Model.php';
require_once 'app/core/View.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Route.php';

// ДОБАВЬТЕ ЭТИ СТРОКИ ДЛЯ РЕПОЗИТОРИЕВ
require_once 'app/core/DataRepository.php';
require_once 'app/repositories/FileRepository.php';
require_once 'app/repositories/DatabaseRepository.php';

Route::start();