<?php
require_once 'ValidatorInterface.php';

/**
 * Проверка обязательного поля
 */
class RequiredValidator implements ValidatorInterface {

    public function validate(string $field, $value): ?string {
        if (empty($value)) {
            return "Поле '$field' обязательно для заполнения.";
        }
        return null;
    }
}