<?php

require_once 'classes/Recipe.php';
require_once 'classes/RecipeStorage.php';
require_once 'classes/RequiredValidator.php';
require_once 'classes/LengthValidator.php';

$errors = [];

// Валидаторы
$validators = [
    'title' => [new RequiredValidator(), new LengthValidator(3, 100)],
    'ingredients' => [new RequiredValidator()],
    'instructions' => [new RequiredValidator()],
    'author' => [new RequiredValidator()]
];

// Проверка
foreach ($validators as $field => $rules) {
    foreach ($rules as $validator) {
        $error = $validator->validate($field, $_POST[$field] ?? '');
        if ($error) {
            $errors[] = $error;
        }
    }
}

// Проверка даты
if (!strtotime($_POST['created_at'])) {
    $errors[] = "Некорректная дата.";
}

// Если есть ошибки
if (!empty($errors)) {
    echo "<h3>Ошибки:</h3>";
    foreach ($errors as $e) {
        echo "<p>$e</p>";
    }
    exit;
}

// Создание объекта
$recipe = new Recipe($_POST);

// Сохранение
$storage = new RecipeStorage('data.json');
$storage->save($recipe->toArray());

echo "<h3>Рецепт успешно сохранен!</h3>";
echo "<a href='index.php'>Назад</a>";