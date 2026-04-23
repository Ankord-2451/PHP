<?php
// Функция для форматирования времени
function format_duration($min) {
    if (!is_numeric($min) || $min <= 0) {
        return '—';
    }

    $min = (int)$min;

    if ($min < 60) return $min . ' мин';

    $h = intdiv($min, 60);
    $m = $min % 60;

    return $m ? "{$h} ч {$m} мин" : "{$h} ч";
}

require_once 'classes/RecipeStorage.php';

$storage = new RecipeStorage('data.json');
$data = $storage->getAll();

// Конвертируем массивы в объекты Recipe для совместимости
require_once 'classes/Recipe.php';
$recipes = [];
foreach ($data as $item) {
    $recipes[] = new Recipe($item);
}

// Переключение между нативным и Twig режимом
$mode = $_GET['mode'] ?? 'native';

if ($mode === 'twig') {
    // Twig режим
    require_once 'vendor/autoload.php';
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates_twig');
    $twig   = new \Twig\Environment($loader, ['debug' => true]);

    // Регистрация кастомного фильтра
    $twig->addFilter(new \Twig\TwigFilter('format_duration', function($min): string {
        if (!is_numeric($min) || $min <= 0) {
            return '—';
        }

        $min = (int)$min;

        if ($min < 60) return $min . ' мин';

        $h = intdiv($min, 60);
        $m = $min % 60;

        return $m ? "{$h} ч {$m} мин" : "{$h} ч";
    }));

    // Конвертируем объекты в массивы для Twig
    $recipesArray = array_map(function($recipe) {
        return $recipe->toArray();
    }, $recipes);

    echo $twig->render('list.html.twig', ['recipes' => $recipesArray]);
} else {
    // Нативный PHP режим
    $pageTitle = 'Список рецептов';
    extract(['recipes' => $recipes, 'pageTitle' => $pageTitle]);
    include 'templates/list.php';
}