<?php
$title = 'Добавить рецепт';
include 'templates/header.php';
?>

<div class="container">
    <?php
    $formTitle = 'Добавить рецепт';
    $formAction = 'save.php';
    $isEdit = false;
    $recipe = [];
    include 'templates/form.php';
    ?>
</div>

<?php include 'templates/footer.php'; ?>