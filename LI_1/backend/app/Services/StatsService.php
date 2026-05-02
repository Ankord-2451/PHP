<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\RedisClient;
use App\Core\Response;
use App\Models\PropertyModel;

/**
 * Handles Redis-backed statistics and view counters.
 */
class StatsService
{
    private $redis;
    private PropertyModel $propertyModel;

    public function __construct()
    {
        $this->redis = RedisClient::getInstance();
        $this->propertyModel = new PropertyModel();
    }

    public function incrementPropertyView(int $propertyId): int
    {
        return $this->redis->incr('views:property:' . $propertyId);
    }

    public function getPropertyViews(int $propertyId): int
    {
        return (int)$this->redis->get('views:property:' . $propertyId) ?: 0;
    }

    public function getSiteStats(): array
    {
        $totalBookings = (int)$this->redis->get('stats:total_bookings') ?: 0;
        $propertyViews = [];
        $properties = $this->propertyModel->findAll();

        foreach ($properties as $property) {
            $id = (int)$property['id'];
            $propertyViews[] = [
                'id' => $id,
                'name' => $property['name'],
                'views' => $this->getPropertyViews($id),
            ];
        }

        usort($propertyViews, fn ($a, $b) => $b['views'] <=> $a['views']);
        return ['total_bookings' => $totalBookings, 'top_properties' => array_slice($propertyViews, 0, 5)];
    }
}
