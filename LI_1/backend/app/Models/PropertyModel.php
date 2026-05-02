<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model for property persistence and queries.
 */
class PropertyModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM properties WHERE id = :id AND is_active = true');
        $stmt->execute([':id' => $id]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        return $property ?: null;
    }

    public function findAll(array $filters = []): array
    {
        $sql = 'SELECT * FROM properties WHERE is_active = true';
        $params = [];

        if (!empty($filters['location'])) {
            $sql .= ' AND location ILIKE :location';
            $params[':location'] = '%' . $filters['location'] . '%';
        }

        if (!empty($filters['property_type'])) {
            $sql .= ' AND property_type = :property_type';
            $params[':property_type'] = $filters['property_type'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= ' AND price_per_day <= :max_price';
            $params[':max_price'] = $filters['max_price'];
        }

        if (!empty($filters['min_capacity'])) {
            $sql .= ' AND capacity >= :min_capacity';
            $params[':min_capacity'] = $filters['min_capacity'];
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO properties (name, description, location, price_per_day, capacity, property_type, amenities, is_active)
            VALUES (:name, :description, :location, :price_per_day, :capacity, :property_type, :amenities, :is_active)
            RETURNING *'
        );

        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':location' => $data['location'],
            ':price_per_day' => $data['price_per_day'],
            ':capacity' => $data['capacity'],
            ':property_type' => $data['property_type'],
            ':amenities' => $data['amenities'] ?? null,
            ':is_active' => $data['is_active'] ?? true,
        ]);

        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        return $property ?: [];
    }

    public function update(int $id, array $data): ?array
    {
        $stmt = $this->db->prepare(
            'UPDATE properties SET name = :name, description = :description, location = :location,
            price_per_day = :price_per_day, capacity = :capacity, property_type = :property_type,
            amenities = :amenities, is_active = :is_active WHERE id = :id RETURNING *'
        );

        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':location' => $data['location'],
            ':price_per_day' => $data['price_per_day'],
            ':capacity' => $data['capacity'],
            ':property_type' => $data['property_type'],
            ':amenities' => $data['amenities'] ?? null,
            ':is_active' => $data['is_active'] ?? true,
            ':id' => $id,
        ]);

        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        return $property ?: null;
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE properties SET is_active = false WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
