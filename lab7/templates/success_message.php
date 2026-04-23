<?php
/**
 * Шаблон для сообщения об успехе
 * 
 * @var string $message - текст сообщения
 */
?>

<div class="success">
    <h3><?= htmlspecialchars($message) ?></h3>
    <a href="index.php">← Назад</a>
</div>
