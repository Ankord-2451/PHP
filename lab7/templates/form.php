<h1><?= htmlspecialchars($formTitle ?? 'Форма рецепта') ?></h1>
<form method="POST" action="<?= htmlspecialchars($formAction ?? 'save.php') ?>">
  <?php if ($isEdit ?? false): ?>
    <input type="hidden" name="id" value="<?= htmlspecialchars($recipeId ?? '') ?>">
  <?php endif; ?>

  <label>Название:
    <input type="text" name="title" value="<?= htmlspecialchars($recipe['title'] ?? '') ?>" required minlength="3">
  </label><br>

  <label>Ингредиенты:
    <textarea name="ingredients" required><?= htmlspecialchars($recipe['ingredients'] ?? '') ?></textarea>
  </label><br>

  <label>Инструкция:
    <textarea name="instructions" required><?= htmlspecialchars($recipe['instructions'] ?? '') ?></textarea>
  </label><br>

  <label>Категория:
    <select name="category">
      <?php foreach (['Суп', 'Десерт', 'Основное'] as $cat): ?>
        <option <?= ($recipe['category'] ?? '') === $cat ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <label>Время приготовления (мин):
    <input type="number" name="prep_time" value="<?= htmlspecialchars($recipe['prep_time'] ?? '') ?>">
  </label><br>

  <label>Сложность:
    <?php foreach (['Легко', 'Средне', 'Сложно'] as $diff): ?>
      <input type="radio" name="difficulty" value="<?= htmlspecialchars($diff) ?>" 
        <?= ($recipe['difficulty'] ?? '') === $diff ? 'checked' : '' ?>>
      <?= htmlspecialchars($diff) ?>
    <?php endforeach; ?>
  </label><br>

  <label>Дата:
    <input type="date" name="created_at" value="<?= htmlspecialchars($recipe['created_at'] ?? date('Y-m-d')) ?>" required>
  </label><br>

  <label>Автор:
    <input type="text" name="author" value="<?= htmlspecialchars($recipe['author'] ?? '') ?>" required>
  </label><br>

  <button type="submit"><?= htmlspecialchars($buttonText ?? 'Сохранить') ?></button>
</form>
