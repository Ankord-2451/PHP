<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\PropertyService;
use App\Services\StatsService;

/**
 * Handles property routes and JSON responses.
 */
class PropertyController
{
    private PropertyService $service;
    private StatsService $statsService;

    public function __construct()
    {
        $this->service = new PropertyService();
        $this->statsService = new StatsService();
    }

    public function index(Request $request): void
    {
        $filters = $request->getQueryParams();
        $properties = $this->service->getAll($filters);
        Response::success($properties);
    }

    public function show(Request $request, string $id): void
    {
        $property = $this->service->getById((int)$id);
        $views = $this->statsService->incrementPropertyView((int)$id);
        $property['views'] = $views;
        Response::success($property);
    }

    public function store(Request $request): void
    {
        $property = $this->service->create($request->getBody());
        Response::success($property, 'Property created successfully', 201);
    }

    public function update(Request $request, string $id): void
    {
        $property = $this->service->update((int)$id, $request->getBody());
        Response::success($property, 'Property updated successfully');
    }

    public function destroy(Request $request, string $id): void
    {
        $this->service->delete((int)$id);
    }
}
