<?php

/**
 * Класс рецепта
 */
class Recipe {

    public string $title;
    public string $ingredients;
    public string $instructions;
    public string $category;
    public int $prep_time;
    public string $difficulty;
    public string $created_at;
    public string $author;

    /**
     * Конструктор
     */
    public function __construct(array $data) {
        $this->title = $data['title'];
        $this->ingredients = $data['ingredients'];
        $this->instructions = $data['instructions'];
        $this->category = $data['category'];
        $this->prep_time = (int)$data['prep_time'];
        $this->difficulty = $data['difficulty'];
        $this->created_at = date('Y-m-d');
        $this->author = $data['author'];
    }

    /**
     * Преобразование в массив
     */
    public function toArray(): array {
        return get_object_vars($this);
    }

    /**
     * Геттеры для совместимости с шаблонами
     */
    public function getName(): string {
        return $this->title;
    }

    public function getPrepTime(): int {
        return $this->prep_time;
    }

    public function getIngredients(): string {
        return $this->ingredients;
    }

    public function getInstructions(): string {
        return $this->instructions;
    }

    public function getCategory(): string {
        return $this->category;
    }

    public function getDifficulty(): string {
        return $this->difficulty;
    }

    public function getCreatedAt(): string {
        return $this->created_at;
    }

    public function getAuthor(): string {
        return $this->author;
    }
}