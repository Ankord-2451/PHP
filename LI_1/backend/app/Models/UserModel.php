<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model for user persistence and queries.
 */
class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role) RETURNING id, name, email, role, created_at'
        );

        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role' => $data['role'] ?? 'user',
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: [];
    }

    public function updateRole(int $id, string $role): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET role = :role WHERE id = :id');
        return $stmt->execute([':role' => $role, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
