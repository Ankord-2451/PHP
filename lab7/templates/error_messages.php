<?php
/**
 * Шаблон для отображения сообщений об ошибках
 * 
 * @var array $errors - массив сообщений об ошибках
 */
?>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <h3>Ошибки:</h3>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
