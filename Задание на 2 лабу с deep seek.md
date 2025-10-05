**Задача:** Создать универсальный класс/интерфейс для работы с данными с двумя реализациями: для БД и для файлов.

**Требования:**
- Получение всех элементов
- Поиск по критерию (поле = значение)
- Сохранение текущей записи
- Удаление текущей записи
- Статическая фабрика для создания из данных
- Использовать для моделей Blog и Guestbook
- Исправить кривое сохранение Guestbook

**Примерная структура:**

```php
interface DataRepository
{
    public function all();
    public function find($field, $value);
    public function save($data);
    public function delete($id);
    public static function createFromData($data);
}

// Реализации:
// - DatabaseRepository (для БД)
// - FileRepository (для файлов с ID)
```

Использовать этот интерфейс для Blog и Guestbook моделей, попутно исправив проблемы с парсингом и сохранением Guestbook.