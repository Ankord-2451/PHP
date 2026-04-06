<?php
require_once 'classes/RecipeStorage.php';

$storage = new RecipeStorage('data.json');

$id = (int)$_POST['id'];

$updatedRecipe = [
    'title' => $_POST['title'],
    'ingredients' => $_POST['ingredients'],
    'instructions' => $_POST['instructions'],
    'category' => $_POST['category'],
    'prep_time' => $_POST['prep_time'],
    'difficulty' => $_POST['difficulty'],
    'created_at' => date('Y-m-d'),
    'author' => $_POST['author']
];

$storage->update($id, $updatedRecipe);

header("Location: index.php");
exit;