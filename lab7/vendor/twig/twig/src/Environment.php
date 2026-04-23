<?php

namespace Twig;

/**
 * Минимальная имитация Twig Environment
 */
class Environment {
    private $loader;
    private $filters = [];
    private $blocks = [];

    public function __construct(Loader\LoaderInterface $loader) {
        $this->loader = $loader;
    }

    public function addFilter(TwigFilter $filter) {
        $this->filters[$filter->getName()] = $filter->getCallback();
    }

    public function render($name, $context = []) {
        $template = $this->loader->getSource($name);
        return $this->renderTemplate($template, $context);
    }

    private function renderTemplate($content, $context) {
        // Обработка extends и блоков
        $content = $this->processExtends($content, $context);
        
        // Основная обработка
        $content = $this->processFor($content, $context);
        $content = $this->processVariables($content, $context);
        $content = $this->processBlocks($content);
        
        return $content;
    }

    private function processVariables($content, $context) {
        // Обработка {{ var | filter }}
        return preg_replace_callback('/\{\{(.+?)\}\}/', function($matches) use ($context) {
            $expr = trim($matches[1]);
            
            // Разделяем на переменную и фильтры
            $parts = array_map('trim', explode('|', $expr));
            $varExpr = array_shift($parts);
            $value = $this->getValueFromContext($varExpr, $context);
            
            // Применяем фильтры
            foreach ($parts as $filterExpr) {
                if (isset($this->filters[$filterExpr])) {
                    $value = call_user_func($this->filters[$filterExpr], $value);
                } elseif (strpos($filterExpr, '(') !== false) {
                    // Фильтр с параметрами
                    preg_match('/(\w+)\((.*?)\)/', $filterExpr, $m);
                    if (isset($this->filters[$m[1]])) {
                        $value = call_user_func($this->filters[$m[1]], $value);
                    }
                }
            }
            
            return htmlspecialchars((string)$value);
        }, $content);
    }

    private function getValueFromContext($expr, $context) {
        $expr = trim($expr);
        
        // Handle string literals
        if ((strpos($expr, "'") === 0 && strrpos($expr, "'") === strlen($expr) - 1) ||
            (strpos($expr, '"') === 0 && strrpos($expr, '"') === strlen($expr) - 1)) {
            return substr($expr, 1, -1);
        }
        
        // Handle array access like recipe['title']
        if (preg_match('/^(\w+)\[([\'"])(.+?)\2\]$/', $expr, $matches)) {
            $objName = $matches[1];
            $propName = $matches[3];
            $obj = $context[$objName] ?? null;
            if (is_array($obj) && isset($obj[$propName])) {
                return $obj[$propName];
            }
            return '';
        }
        
        // recipe.name -> recipe->getName() или recipe->name
        if (strpos($expr, '.') !== false) {
            list($objName, $propName) = explode('.', $expr, 2);
            $obj = $context[$objName] ?? null;
            
            if ($obj && is_object($obj)) {
                // Пробуем метод getXxx
                $method = 'get' . ucfirst($this->camelCase($propName));
                if (method_exists($obj, $method)) {
                    return call_user_func([$obj, $method]);
                }
                // Пробуем свойство
                if (property_exists($obj, $propName)) {
                    return $obj->$propName;
                }
                // Пробуем свойство с snake_case
                $snakeCase = $this->snakeCase($propName);
                if (property_exists($obj, $snakeCase)) {
                    return $obj->$snakeCase;
                }
            } elseif (is_array($obj)) {
                return $obj[$propName] ?? '';
            }
            return '';
        }
        
        // Простая переменная
        return $context[$expr] ?? '';
    }

    private function processFor($content, $context) {
        // {% for i, recipe in recipes %}...{% endfor %}
        // {% for recipe in recipes %}...{% endfor %}
        return preg_replace_callback(
            '/\{%for (\w+) in (\w+)%\}(.*?)\{%endfor%\}/s',
            function($matches) use ($context) {
                $itemVar = $matches[1];
                $arrayName = $matches[2];
                $body = $matches[3];
                
                $array = $context[$arrayName] ?? [];
                $output = '';
                
                foreach ($array as $key => $item) {
                    $itemContext = $context;
                    $itemContext[$itemVar] = $item;
                    $itemContext['loop'] = [
                        'index0' => $key,
                        'index' => $key + 1,
                        'first' => $key === 0,
                        'last' => $key === count($array) - 1,
                    ];
                    $output .= $this->processVariables($body, $itemContext);
                }
                
                return $output;
            },
            $content
        );
    }

    private function processExtends($content, $context) {
        // {% extends 'base.html.twig' %}
        if (preg_match('/\{%\s*extends\s+[\'"](.+?)[\'"]\s*%\}/i', $content, $matches)) {
            $parentName = $matches[1];
            $parentContent = $this->loader->getSource($parentName);
            $childContent = preg_replace('/\{%\s*extends\s+[\'"](.+?)[\'"]\s*%\}/', '', $content);
            
            // Извлекаем блоки из child
            $blocks = [];
            preg_match_all('/\{%\s*block\s+(\w+)\s*%\}(.*?)\{%\s*endblock\s*%\}/s', $childContent, $m);
            foreach ($m[1] as $i => $blockName) {
                $blocks[$blockName] = $m[2][$i];
            }
            
            // Заменяем блоки в parent
            $result = preg_replace_callback(
                '/\{%\s*block\s+(\w+)\s*%\}(.*?)\{%\s*endblock\s*%\}/s',
                function($m) use ($blocks) {
                    $blockName = $m[1];
                    return isset($blocks[$blockName]) ? $blocks[$blockName] : $m[2];
                },
                $parentContent
            );
            
            return $result;
        }
        return $content;
    }

    private function processBlocks($content) {
        // Убираем оставшиеся теги блоков
        return preg_replace('/\{%\s*block\s+\w+\s*%\}(.*?)\{%\s*endblock\s*%\}/s', '$1', $content);
    }

    private function camelCase($str) {
        return lcfirst(implode('', array_map('ucfirst', explode('_', $str))));
    }

    private function snakeCase($str) {
        return strtolower(preg_replace('/([A-Z])/', '_$1', $str));
    }
}
