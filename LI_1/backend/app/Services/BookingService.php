<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\RedisClient;
use App\Core\Response;
use App\Core\Validator;
use App\Models\BookingModel;
use App\Models\PropertyModel;

/**
 * Handles booking business logic, validation, and cache invalidation.
 */
class BookingService
{
    private BookingModel $model;
    private PropertyModel $propertyModel;
    private $redis;

    public function __construct()
    {
        $this->model = new BookingModel();
        $this->propertyModel = new PropertyModel();
        $this->redis = RedisClient::getInstance();
    }

    public function getUserBookings(int $userId): array
    {
        $cacheKey = 'cache:bookings:user:' . $userId;
        $cached = $this->redis->get($cacheKey);
        if ($cached !== null) {
            return json_decode($cached, true);
        }

        $bookings = $this->model->findByUserId($userId);
        $this->redis->setex($cacheKey, 120, json_encode($bookings));
        return $bookings;
    }

    public function create(int $userId, array $data): array
    {
        $validator = new Validator($data);
        $validator->required('property_id')->positiveNumber('property_id')
            ->required('date_from')->date('date_from')
            ->required('date_to')->date('date_to')
            ->dateBefore('date_from', 'date_to')
            ->required('guests')->positiveNumber('guests')
            ->required('contact_phone')->minLength('contact_phone', 7)
            ->maxLength('notes', 500);

        if ($validator->fails()) {
            Response::error($validator->errors(), 422);
        }

        $property = $this->propertyModel->findById((int)$data['property_id']);
        if ($property === null) {
            Response::error('Property not found', 404);
        }

        if ((int)$data['guests'] > (int)$property['capacity']) {
            Response::error(['guests' => 'Guest count exceeds property capacity.'], 422);
        }

        $days = (new \DateTime($data['date_to']))->diff(new \DateTime($data['date_from']))->days;
        if ($days < 1) {
            Response::error(['date_to' => 'End date must be after start date.'], 422);
        }

        $totalPrice = (float)$property['price_per_day'] * $days * (int)$data['guests'];
        $booking = $this->model->create([
            'user_id' => $userId,
            'property_id' => $data['property_id'],
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
            'guests' => $data['guests'],
            'notes' => $data['notes'] ?? null,
            'contact_phone' => $data['contact_phone'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        $this->redis->del('cache:bookings:user:' . $userId);
        $this->redis->incr('stats:total_bookings');

        return $booking;
    }

    public function cancel(int $userId, int $bookingId): void
    {
        $booking = $this->model->findById($bookingId);
        if ($booking === null || (int)$booking['user_id'] !== $userId) {
            Response::error('Booking not found or access denied', 404);
        }

        if (!$this->model->cancel($bookingId)) {
            Response::error('Unable to cancel booking', 500);
        }

        $this->redis->del('cache:bookings:user:' . $userId);
    }

    public function updateStatus(int $bookingId, string $status): array
    {
        if (!in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
            Response::error(['status' => 'Invalid booking status.'], 422);
        }

        $booking = $this->model->updateStatus($bookingId, $status);
        if ($booking === null) {
            Response::error('Booking not found', 404);
        }

        $this->redis->del('cache:bookings:user:' . $booking['user_id']);
        return $booking;
    }
}
