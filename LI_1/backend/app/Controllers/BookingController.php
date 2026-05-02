<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\JwtHelper;
use App\Services\BookingService;

/**
 * Handles booking endpoints for authenticated users.
 */
class BookingController
{
    private BookingService $service;

    public function __construct()
    {
        $this->service = new BookingService();
    }

    public function index(Request $request): void
    {
        $token = $this->resolveToken($request);
        $payload = JwtHelper::decode($token);
        $bookings = $this->service->getUserBookings((int)$payload['user_id']);
        Response::success($bookings);
    }

    public function store(Request $request): void
    {
        $token = $this->resolveToken($request);
        $payload = JwtHelper::decode($token);
        $booking = $this->service->create((int)$payload['user_id'], $request->getBody());
        Response::success($booking, 'Booking created successfully', 201);
    }

    public function update(Request $request, string $id): void
    {
        $body = $request->getBody();
        $status = $body['status'] ?? '';
        $booking = $this->service->updateStatus((int)$id, $status);
        Response::success($booking, 'Booking status updated successfully');
    }

    public function cancel(Request $request, string $id): void
    {
        $token = $this->resolveToken($request);
        $payload = JwtHelper::decode($token);
        $this->service->cancel((int)$payload['user_id'], (int)$id);
        Response::success(null, 'Booking cancelled successfully');
    }

    private function resolveToken(Request $request): string
    {
        $header = $request->getHeader('Authorization');
        if ($header !== null && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        Response::error('Unauthorized', 401);
    }
}
