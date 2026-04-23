<?php
/**
 * Шаблон для формы рецепта
 * Используется в: save.php и edit.php
 * 
 * @var string $title - заголовок формы
 * @var string $action - путь для отправки формы
 * @var array $recipe - данные рецепта (если редактирование)
 * @var bool $isEdit - флаг редактирования
 */
?>

<h2><?= $title ?></h2>
<div class="form">
<form method="POST" action="<?= $action ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $recipe['id'] ?? '' ?>">
    <?php endif; ?>

    Название: 
    <input type="text" name="title" value="<?= $recipe['title'] ?? '' ?>" required minlength="3"><br>

    Ингредиенты:<br>
    <textarea name="ingredients" required><?= $recipe['ingredients'] ?? '' ?></textarea><br>

    Инструкция:<br>
    <textarea name="instructions" required><?= $recipe['instructions'] ?? '' ?></textarea><br>

    Категория:
    <select name="category">
        <?php $categories = ['Суп', 'Десерт', 'Основное']; ?>
        <?php foreach ($categories as $cat): ?>
            <option <?= ($recipe['category'] ?? '') === $cat ? 'selected' : '' ?>>
                <?= $cat ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    Время приготовления:
    <input type="number" name="prep_time" value="<?= $recipe['prep_time'] ?? '' ?>"><br>

    Сложность:
    <?php $difficulties = ['Легко', 'Средне', 'Сложно']; ?>
    <?php foreach ($difficulties as $diff): ?>
        <input type="radio" name="difficulty" value="<?= $diff ?>" 
            <?= ($recipe['difficulty'] ?? '') === $diff ? 'checked' : '' ?>>
        <?= $diff ?>
    <?php endforeach; ?><br>

    Дата:
    <input type="date" name="created_at" value="<?= $recipe['created_at'] ?? date('Y-m-d') ?>" required><br>

    Автор:
    <input type="text" name="author" value="<?= $recipe['author'] ?? '' ?>" required><br>

    <button type="submit">Сохранить</button>
</form>
</div>
