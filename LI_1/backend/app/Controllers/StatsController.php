<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\StatsService;

/**
 * Handles statistics endpoints.
 */
class StatsController
{
    private StatsService $service;

    public function __construct()
    {
        $this->service = new StatsService();
    }

    public function propertyViews(Request $request, string $id): void
    {
        $views = $this->service->getPropertyViews((int)$id);
        Response::success(['property_id' => (int)$id, 'views' => $views]);
    }

    public function siteStats(Request $request): void
    {
        $stats = $this->service->getSiteStats();
        Response::success($stats);
    }
}
