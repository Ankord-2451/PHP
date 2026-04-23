<?php

class RecipeStorage {

    private string $file;

    public function __construct(string $file) {
        $this->file = $file;
    }

    public function getAll(): array {
        if (!file_exists($this->file)) {
            return [];
        }
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    public function save(array $recipe): void {
        $data = $this->getAll();
        $data[] = $recipe;
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Удаление рецепта по ID
     */
    public function delete(int $id): void {
        $data = $this->getAll();
        unset($data[$id]);
        file_put_contents($this->file, json_encode(array_values($data), JSON_PRETTY_PRINT));
    }

    /**
     * Обновление рецепта
     */
    public function update(int $id, array $newData): void {
        $data = $this->getAll();
        $data[$id] = $newData;
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }
}