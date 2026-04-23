<?php
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

// Если есть ошибки
if (!empty($errors)) {
    include 'templates/header.php';
    include 'templates/error_messages.php';
    include 'templates/footer.php';
    exit;
}

$storage = new RecipeStorage('data.json');
$id = (int)$_POST['id'];

$updatedRecipe = [
    'title' => $_POST['title'],
    'ingredients' => $_POST['ingredients'],
    'instructions' => $_POST['instructions'],
    'category' => $_POST['category'],
    'prep_time' => $_POST['prep_time'],
    'difficulty' => $_POST['difficulty'],
    'created_at' => $_POST['created_at'],
    'author' => $_POST['author']
];

$storage->update($id, $updatedRecipe);

include 'templates/header.php';
$message = "Рецепт успешно обновлен!";
include 'templates/success_message.php';
include 'templates/footer.php';