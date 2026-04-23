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
    include 'templates/header.php';
    include 'templates/error_messages.php';
    include 'templates/footer.php';
    exit;
}

// Создание объекта
$recipe = new Recipe($_POST);

// Сохранение
$storage = new RecipeStorage('data.json');
$storage->save($recipe->toArray());

include 'templates/header.php';
$message = "Рецепт успешно сохранен!";
include 'templates/success_message.php';
include 'templates/footer.php';