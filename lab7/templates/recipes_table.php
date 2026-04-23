<?php
/**
 * Шаблон для таблицы рецептов
 * 
 * @var array $data - массив всех рецептов
 */
?>

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
    <td><?= htmlspecialchars($r['title']) ?></td>
    <td><?= htmlspecialchars($r['category']) ?></td>
    <td><?= htmlspecialchars($r['difficulty']) ?></td>
    <td><?= htmlspecialchars($r['created_at']) ?></td>
    <td><?= htmlspecialchars($r['author']) ?></td>
    <td>
    <a href="edit.php?id=<?= $index ?>">✏️</a>
    <a href="delete.php?id=<?= $index ?>" onclick="return confirm('Удалить?')">❌</a>
    </td>
</tr>
<?php endforeach; ?>

</table>
