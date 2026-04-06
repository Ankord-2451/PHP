<?php
require_once 'ValidatorInterface.php';

/**
 * Проверка длины строки
 */
class LengthValidator implements ValidatorInterface {

    private int $min;
    private int $max;

    public function __construct(int $min, int $max) {
        $this->min = $min;
        $this->max = $max;
    }

    public function validate(string $field, $value): ?string {
        $len = strlen($value);
        if ($len < $this->min || $len > $this->max) {
            return "Поле '$field' должно быть от $this->min до $this->max символов.";
        }
        return null;
    }
}