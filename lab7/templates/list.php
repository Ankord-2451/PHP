<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Список рецептов</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Рецепты</h1>
<a href="form.php">Добавить рецепт</a>
<table border="1">
  <thead>
    <tr>
      <th>Название</th>
      <th>Время приготовления</th>
      <th>Действия</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($recipes as $i => $recipe): ?>
    <tr>
      <td><?= htmlspecialchars($recipe->getName()) ?></td>
      <td><?= format_duration($recipe->getPrepTime()) ?></td>
      <td>
        <a href="edit.php?id=<?= $i ?>">✏️ Изменить</a>
        <a href="delete.php?id=<?= $i ?>" onclick="return confirm('Удалить?')">❌ Удалить</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</body>
</html>
