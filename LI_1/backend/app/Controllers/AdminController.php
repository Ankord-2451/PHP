<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\AdminService;

/**
 * Handles admin dashboard endpoints.
 */
class AdminController
{
    private AdminService $service;

    public function __construct()
    {
        $this->service = new AdminService();
    }

    public function users(Request $request): void
    {
        $users = $this->service->getAllUsers();
        Response::success($users);
    }

    public function changeRole(Request $request, string $id): void
    {
        $body = $request->getBody();
        $user = $this->service->changeUserRole((int)$id, $body['role'] ?? '');
        Response::success($user, 'User role updated successfully');
    }

    public function deleteUser(Request $request, string $id): void
    {
        $this->service->deleteUser((int)$id);
        Response::success(null, 'User deleted successfully');
    }

    public function allBookings(Request $request): void
    {
        $bookings = $this->service->getAllBookings();
        Response::success($bookings);
    }

    public function stats(Request $request): void
    {
        $stats = $this->service->getDashboardStats();
        Response::success($stats);
    }

    public function createAdminUser(Request $request): void
    {
        $user = $this->service->createAdminUser($request->getBody());
        Response::success($user, 'Admin user created successfully', 201);
    }
}
