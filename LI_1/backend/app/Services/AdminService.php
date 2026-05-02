<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Response;
use App\Models\UserModel;
use App\Models\PropertyModel;
use App\Models\BookingModel;
use App\Services\StatsService;

/**
 * Handles admin-level management operations.
 */
class AdminService
{
    private UserModel $userModel;
    private PropertyModel $propertyModel;
    private BookingModel $bookingModel;
    private StatsService $statsService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->propertyModel = new PropertyModel();
        $this->bookingModel = new BookingModel();
        $this->statsService = new StatsService();
    }

    public function getAllUsers(): array
    {
        return $this->userModel->findAll();
    }

    public function changeUserRole(int $id, string $role): array
    {
        if (!in_array($role, ['user', 'admin'], true)) {
            Response::error(['role' => 'Role must be user or admin.'], 422);
        }

        if (!$this->userModel->updateRole($id, $role)) {
            Response::error('User not found', 404);
        }

        return $this->userModel->findById($id) ?? [];
    }

    public function deleteUser(int $id): void
    {
        if (!$this->userModel->delete($id)) {
            Response::error('User not found', 404);
        }
    }

    public function getAllBookings(): array
    {
        return $this->bookingModel->findAll();
    }

    public function getDashboardStats(): array
    {
        $users = $this->userModel->findAll();
        $properties = $this->propertyModel->findAll();
        $bookings = $this->getAllBookings();
        $stats = $this->statsService->getSiteStats();

        return [
            'total_users' => count($users),
            'total_properties' => count($properties),
            'total_bookings' => count($bookings),
            'top_properties' => $stats['top_properties'],
        ];
    }

    public function createAdminUser(array $data): array
    {
        $validator = new \App\Core\Validator($data);
        $validator->required('name')->minLength('name', 2)->maxLength('name', 100)
            ->required('email')->email('email')
            ->required('password')->minLength('password', 8);

        if ($validator->fails()) {
            Response::error($validator->errors(), 422);
        }

        if ($this->userModel->findByEmail($data['email']) !== null) {
            Response::error(['email' => 'Email is already registered.'], 422);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        return $this->userModel->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'role' => 'admin',
        ]);
    }
}
