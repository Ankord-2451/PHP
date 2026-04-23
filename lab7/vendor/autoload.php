<?php
/**
 * Простой autoloader для Twig
 * Имитирует composer autoload
 */

spl_autoload_register(function ($class) {
    $prefix = 'Twig\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $file = __DIR__ . '/twig/twig/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
