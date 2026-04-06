<?php
require_once 'classes/RecipeStorage.php';

$storage = new RecipeStorage('data.json');
$data = $storage->getAll();

$id = $_GET['id'];
$recipe = $data[$id];
?>
<body>
    <link rel="stylesheet" href="style.css">
</body>

<h2>Редактировать рецепт</h2>
<div class="form">
<form method="POST" action="update.php">
    <input type="hidden" name="id" value="<?= $id ?>">

    Название:
    <input type="text" name="title" value="<?= $recipe['title'] ?>">

    Ингредиенты:
    <textarea name="ingredients"><?= $recipe['ingredients'] ?></textarea>

    Инструкция:
    <textarea name="instructions"><?= $recipe['instructions'] ?></textarea>

    Категория:
    <input type="text" name="category" value="<?= $recipe['category'] ?>">

    Время:
    <input type="number" name="prep_time" value="<?= $recipe['prep_time'] ?>">

    Сложность:
    <input type="text" name="difficulty" value="<?= $recipe['difficulty'] ?>">

    Автор:
    <input type="text" name="author" value="<?= $recipe['author'] ?>">

    <button>Сохранить</button>
</form>
</div>