<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Model for booking persistence and queries.
 */
class BookingModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM bookings WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        return $booking ?: null;
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT b.*, p.name AS property_name, p.location, p.property_type FROM bookings b JOIN properties p ON b.property_id = p.id WHERE b.user_id = :user_id ORDER BY b.created_at DESC');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): array
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bookings (user_id, property_id, date_from, date_to, guests, notes, contact_phone, total_price, status)
            VALUES (:user_id, :property_id, :date_from, :date_to, :guests, :notes, :contact_phone, :total_price, :status)
            RETURNING *'
        );

        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':property_id' => $data['property_id'],
            ':date_from' => $data['date_from'],
            ':date_to' => $data['date_to'],
            ':guests' => $data['guests'],
            ':notes' => $data['notes'] ?? null,
            ':contact_phone' => $data['contact_phone'],
            ':total_price' => $data['total_price'],
            ':status' => $data['status'] ?? 'pending',
        ]);

        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        return $booking ?: [];
    }

    public function updateStatus(int $id, string $status): ?array
    {
        $stmt = $this->db->prepare('UPDATE bookings SET status = :status WHERE id = :id RETURNING *');
        $stmt->execute([':status' => $status, ':id' => $id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        return $booking ?: null;
    }

    public function cancel(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE bookings SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => 'cancelled', ':id' => $id]);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT b.*, u.name AS user_name, u.email AS user_email, p.name AS property_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN properties p ON b.property_id = p.id ORDER BY b.created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
