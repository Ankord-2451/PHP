<?php
require_once 'classes/RecipeStorage.php';

$storage = new RecipeStorage('data.json');
$data = $storage->getAll();

// сортировка
if (isset($_GET['sort'])) {
    usort($data, function ($a, $b) {
        return strcmp($a[$_GET['sort']], $b[$_GET['sort']]);
    });
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Рецепты</title>
</head>
<body>

<h2>Добавить рецепт</h2>
<div class="form">
<form method="POST" action="save.php">
    Название: <input type="text" name="title" required minlength="3"><br>

    Ингредиенты:<br>
    <textarea name="ingredients" required></textarea><br>

    Инструкция:<br>
    <textarea name="instructions" required></textarea><br>

    Категория:
    <select name="category">
        <option>Суп</option>
        <option>Десерт</option>
        <option>Основное</option>
    </select><br>

    Время приготовления:
    <input type="number" name="prep_time"><br>

    Сложность:
    <input type="radio" name="difficulty" value="Легко">Легко
    <input type="radio" name="difficulty" value="Средне">Средне
    <input type="radio" name="difficulty" value="Сложно">Сложно<br>

    Дата:
    <input type="date" name="created_at" required><br>

    Автор:
    <input type="text" name="author" required><br>

    <button type="submit">Сохранить</button>
</form>
</div>
<hr>

<h2>Список рецептов</h2>

<a href="?sort=title">Сортировать по названию</a> |
<a href="?sort=category">По категории</a>

<table border="1">
<tr>
    <th>Название</th>
    <th>Категория</th>
    <th>Сложность</th>
    <th>Дата</th>
    <th>Автор</th>
    <th>Действия</th>
</tr>

<?php foreach ($data as $index => $r): ?>
<tr>
    <td><?= $r['title'] ?></td>
    <td><?= $r['category'] ?></td>
    <td><?= $r['difficulty'] ?></td>
    <td><?= $r['created_at'] ?></td>
    <td><?= $r['author'] ?></td>
    <td>
    <a href="edit.php?id=<?= $index ?>">✏️</a>
    <a href="delete.php?id=<?= $index ?>" onclick="return confirm('Удалить?')">❌</a>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>