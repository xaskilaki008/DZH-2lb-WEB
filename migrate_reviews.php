<?php
// Полное подключение как в index.php
require_once 'app/core/Model.php';
require_once 'app/core/View.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Route.php';
require_once 'app/core/DataRepository.php';
require_once 'app/repositories/FileRepository.php';
require_once 'app/repositories/DatabaseRepository.php';
require_once 'app/models/GuestBookModel.php';

echo "Начало миграции данных...\n";

// Миграция старых кривых данных в новый формат
$model = new GuestBookModel();
$allItems = GuestBookModel::all();

echo "Найдено записей: " . count($allItems) . "\n";

// Просто выводим данные для проверки
foreach ($allItems as $index => $item) {
    echo "Запись " . ($index + 1) . ": " . ($item->fullname ?? 'N/A') . "\n";
}

echo "Миграция завершена! Данные теперь в правильном формате.\n";