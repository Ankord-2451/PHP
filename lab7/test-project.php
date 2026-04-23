#!/usr/bin/env php
<?php

/**
 * Тестовый скрипт для проверки целостности проекта Recipe Manager
 * Запуск: php test-project.php
 */

echo "================================\n";
echo "  RECIPE MANAGER - PROJECT TEST\n";
echo "================================\n\n";

$passed = 0;
$failed = 0;

// Тест 1: Проверка всех файлов
echo "1. Проверка наличия необходимых файлов...\n";
$required_files = [
    'index.php',
    'save.php',
    'edit.php',
    'update.php',
    'delete.php',
    'style.css',
    'data.json',
    'classes/Recipe.php',
    'classes/RecipeStorage.php',
    'classes/ValidatorInterface.php',
    'classes/RequiredValidator.php',
    'classes/LengthValidator.php',
    'templates/header.php',
    'templates/footer.php',
    'templates/recipe_form.php',
    'templates/recipes_table.php',
    'templates/error_messages.php',
    'templates/success_message.php',
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file\n";
        $passed++;
    } else {
        echo "   ✗ MISSING: $file\n";
        $failed++;
    }
}

// Тест 2: Проверка классов
echo "\n2. Проверка загрузки классов...\n";
require_once 'classes/ValidatorInterface.php';
require_once 'classes/RequiredValidator.php';
require_once 'classes/LengthValidator.php';
require_once 'classes/Recipe.php';
require_once 'classes/RecipeStorage.php';

$classes = [
    'Recipe',
    'RecipeStorage',
    'RequiredValidator',
    'LengthValidator',
    'ValidatorInterface'
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "   ✓ $class loaded\n";
        $passed++;
    } else {
        echo "   ✗ FAILED: $class not loaded\n";
        $failed++;
    }
}

// Тест 3: Проверка RecipeStorage методов
echo "\n3. Проверка методов RecipeStorage...\n";
$storage = new RecipeStorage('data.json');
$methods = ['getAll', 'save', 'update', 'delete'];

foreach ($methods as $method) {
    if (method_exists($storage, $method)) {
        echo "   ✓ RecipeStorage::$method()\n";
        $passed++;
    } else {
        echo "   ✗ MISSING: RecipeStorage::$method()\n";
        $failed++;
    }
}

// Тест 4: Проверка свойств Recipe
echo "\n4. Проверка свойств Recipe...\n";
$recipe_data = [
    'title' => 'Test Recipe',
    'ingredients' => 'Test ingredients',
    'instructions' => 'Test instructions',
    'category' => 'Основное',
    'prep_time' => 30,
    'difficulty' => 'Легко',
    'author' => 'Test Author'
];

$recipe = new Recipe($recipe_data);
$properties = ['title', 'ingredients', 'instructions', 'category', 'prep_time', 'difficulty', 'author'];

foreach ($properties as $prop) {
    if (isset($recipe->$prop)) {
        echo "   ✓ Recipe->\$$prop\n";
        $passed++;
    } else {
        echo "   ✗ MISSING: Recipe->\$$prop\n";
        $failed++;
    }
}

// Тест 5: Проверка шаблонов
echo "\n5. Проверка шаблонов на синтаксис...\n";
$templates = [
    'templates/header.php',
    'templates/footer.php',
    'templates/recipe_form.php',
    'templates/recipes_table.php',
    'templates/error_messages.php',
    'templates/success_message.php'
];

foreach ($templates as $template) {
    $output = shell_exec("php -l $template 2>&1");
    if (strpos($output, 'No syntax errors detected') !== false) {
        echo "   ✓ $template OK\n";
        $passed++;
    } else {
        echo "   ✗ SYNTAX ERROR: $template\n";
        echo "      $output\n";
        $failed++;
    }
}

// Тест 6: Проверка основных PHP-файлов
echo "\n6. Проверка основных PHP-файлов...\n";
$main_files = ['index.php', 'save.php', 'edit.php', 'update.php', 'delete.php'];

foreach ($main_files as $file) {
    $output = shell_exec("php -l $file 2>&1");
    if (strpos($output, 'No syntax errors detected') !== false) {
        echo "   ✓ $file OK\n";
        $passed++;
    } else {
        echo "   ✗ SYNTAX ERROR: $file\n";
        echo "      $output\n";
        $failed++;
    }
}

// Тест 7: Проверка JSON
echo "\n7. Проверка JSON базы данных...\n";
if (file_exists('data.json')) {
    $json_content = file_get_contents('data.json');
    $decoded = json_decode($json_content, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "   ✓ data.json валиден\n";
        echo "   ✓ Количество рецептов: " . count($decoded) . "\n";
        $passed += 2;
    } else {
        echo "   ✗ JSON ERROR: " . json_last_error_msg() . "\n";
        $failed++;
    }
}

// Финальная статистика
echo "\n================================\n";
echo "  РЕЗУЛЬТАТЫ ТЕСТИРОВАНИЯ\n";
echo "================================\n";
echo "✓ PASSED: $passed\n";
echo "✗ FAILED: $failed\n";
echo "TOTAL:  " . ($passed + $failed) . "\n";

if ($failed === 0) {
    echo "\n🎉 ВСЕ ТЕСТЫ ПРОЙДЕНЫ! Приложение готово к использованию.\n";
    exit(0);
} else {
    echo "\n⚠️  Обнаружены проблемы. Проверьте ошибки выше.\n";
    exit(1);
}
