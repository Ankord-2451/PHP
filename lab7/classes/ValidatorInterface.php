<?php

/**
 * Интерфейс валидатора
 */
interface ValidatorInterface {
    /**
     * Проверка значения
     * @param string $field
     * @param mixed $value
     * @return string|null
     */
    public function validate(string $field, $value): ?string;
}