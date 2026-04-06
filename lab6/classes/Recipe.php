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
}