<?php
require_once 'classes/RecipeStorage.php';

$storage = new RecipeStorage('data.json');
$data = $storage->getAll();

$id = $_GET['id'];
$recipe = $data[$id];
$recipe['id'] = $id;

include 'templates/header.php';
?>

<div class="container">
    <?php
    $title = 'Редактировать рецепт';
    $action = 'update.php';
    $isEdit = true;
    include 'templates/recipe_form.php';
    ?>
</div>

<?php include 'templates/footer.php'; ?>