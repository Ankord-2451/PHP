<?php
require_once 'classes/RecipeStorage.php';

if (!isset($_GET['id'])) {
    include 'templates/header.php';
    $errors = ['Ошибка: ID рецепта не указан.'];
    include 'templates/error_messages.php';
    include 'templates/footer.php';
    exit;
}

$storage = new RecipeStorage('data.json');
$id = (int)$_GET['id'];

$data = $storage->getAll();
if (!isset($data[$id])) {
    include 'templates/header.php';
    $errors = ['Ошибка: Рецепт не найден.'];
    include 'templates/error_messages.php';
    include 'templates/footer.php';
    exit;
}

$storage->delete($id);

include 'templates/header.php';
$message = "Рецепт успешно удален!";
include 'templates/success_message.php';
include 'templates/footer.php';