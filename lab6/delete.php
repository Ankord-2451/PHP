<?php
require_once 'classes/RecipeStorage.php';

if (!isset($_GET['id'])) {
    die("Ошибка");
}

$storage = new RecipeStorage('data.json');
$storage->delete((int)$_GET['id']);

header("Location: index.php");
exit;