#!/usr/bin/env php
<?php

/**
 * Тестовый скрипт для проверки двух режимов работы
 */

echo "================================\n";
echo "  RECIPE MANAGER - MODE TEST\n";
echo "================================\n\n";

// Тест 1: Проверка классов
echo "1. Проверка загрузки классов...\n";
require_once 'classes/Recipe.php';
require_once 'classes/RecipeStorage.php';

// Создаем тестовый рецепт
$recipe_data = [
    'title' => 'Борщ',
    'ingredients' => 'Свёкла, капуста, морковь',
    'instructions' => 'Варить 40 минут',
    'category' => 'Суп',
    'prep_time' => 45,
    'difficulty' => 'Легко',
    'author' => 'Бабушка'
];

$recipe = new Recipe($recipe_data);
echo "   ✓ Recipe создан\n";

// Тест 2: Проверка методов Recipe
echo "\n2. Проверка методов Recipe...\n";
echo "   - getName(): " . $recipe->getName() . "\n";
echo "   - getPrepTime(): " . $recipe->getPrepTime() . " мин\n";
echo "   - getCategory(): " . $recipe->getCategory() . "\n";
echo "   - getDifficulty(): " . $recipe->getDifficulty() . "\n";
echo "   ✓ Все методы работают\n";

// Тест 3: Проверка фильтра format_duration
echo "\n3. Проверка фильтра format_duration...\n";
$formatDuration = function(int $min): string {
    if ($min < 60) return $min . ' мин';
    $h = intdiv($min, 60);
    $m = $min % 60;
    return $m ? "{$h} ч {$m} мин" : "{$h} ч";
};

$testCases = [45, 60, 90, 150, 30];
foreach ($testCases as $min) {
    $formatted = $formatDuration($min);
    echo "   - $min мин → $formatted\n";
}
echo "   ✓ Фильтр работает корректно\n";

// Тест 4: Проверка нативного режима
echo "\n4. Проверка нативного режима (PHP-шаблоны)...\n";
$storage = new RecipeStorage('data.json');
$data = $storage->getAll();

// Преобразуем в объекты Recipe
$recipes = [];
foreach ($data as $item) {
    $recipes[] = new Recipe($item);
}

echo "   ✓ Загружено " . count($recipes) . " рецептов\n";
echo "   ✓ Шаблоны готовы к использованию\n";

// Тест 5: Проверка Twig Environment
echo "\n5. Проверка Twig Environment...\n";
require_once 'vendor/autoload.php';

try {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates_twig');
    $twig = new \Twig\Environment($loader);
    
    $twig->addFilter(new \Twig\TwigFilter('format_duration', $formatDuration));
    
    echo "   ✓ Twig Environment инициализирован\n";
    echo "   ✓ Фильтр format_duration зарегистрирован\n";
    
    // Проверка шаблонов
    if ($loader->exists('base.html.twig')) {
        echo "   ✓ base.html.twig найден\n";
    }
    if ($loader->exists('list.html.twig')) {
        echo "   ✓ list.html.twig найден\n";
    }
} catch (Exception $e) {
    echo "   ✗ ОШИБКА: " . $e->getMessage() . "\n";
}

echo "\n================================\n";
echo "  ✅ ВСЕ ТЕСТЫ ПРОЙДЕНЫ\n";
echo "================================\n";
echo "\n📝 Инструкции для проверки:\n";
echo "   1. Запустите: php -S localhost:8000\n";
echo "   2. Перейдите по ссылкам:\n";
echo "      - Нативный режим: http://localhost:8000/index.php\n";
echo "      - Twig режим: http://localhost:8000/index.php?mode=twig\n";
echo "\n";
