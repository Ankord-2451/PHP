<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\RedisClient;
use App\Core\Response;
use App\Core\Validator;
use App\Models\PropertyModel;

/**
 * Handles business logic for property catalog operations.
 */
class PropertyService
{
    private PropertyModel $model;
    private $redis;

    public function __construct()
    {
        $this->model = new PropertyModel();
        $this->redis = RedisClient::getInstance();
    }

    public function getAll(array $filters = []): array
    {
        $cacheKey = empty($filters) ? 'cache:properties:all' : 'cache:properties:search:' . md5(serialize($filters));
        $cached = $this->redis->get($cacheKey);
        if ($cached !== null) {
            return json_decode($cached, true);
        }

        $properties = $this->model->findAll($filters);
        $this->redis->setex($cacheKey, 300, json_encode($properties));
        return $properties;
    }

    public function getById(int $id): array
    {
        $cacheKey = 'cache:property:' . $id;
        $cached = $this->redis->get($cacheKey);
        if ($cached !== null) {
            return json_decode($cached, true);
        }

        $property = $this->model->findById($id);
        if ($property === null) {
            Response::error('Property not found', 404);
        }

        $this->redis->setex($cacheKey, 600, json_encode($property));
        return $property;
    }

    public function create(array $data): array
    {
        $validator = new Validator($data);
        $validator->required('name')->minLength('name', 3)->maxLength('name', 200)
            ->required('price_per_day')->positiveNumber('price_per_day')
            ->required('capacity')->positiveNumber('capacity')
            ->required('property_type')->in('property_type', ['room', 'hall', 'venue', 'apartment'])
            ->required('location')->minLength('location', 2);

        if ($validator->fails()) {
            Response::error($validator->errors(), 422);
        }

        $property = $this->model->create($validator->getData());
        $this->redis->del('cache:properties:all');
        return $property;
    }

    public function update(int $id, array $data): array
    {
        $validator = new Validator($data);
        $validator->required('name')->minLength('name', 3)->maxLength('name', 200)
            ->required('price_per_day')->positiveNumber('price_per_day')
            ->required('capacity')->positiveNumber('capacity')
            ->required('property_type')->in('property_type', ['room', 'hall', 'venue', 'apartment'])
            ->required('location')->minLength('location', 2);

        if ($validator->fails()) {
            Response::error($validator->errors(), 422);
        }

        $property = $this->model->update($id, $validator->getData());
        if ($property === null) {
            Response::error('Property not found', 404);
        }

        $this->redis->del('cache:properties:all');
        $this->redis->del('cache:property:' . $id);
        return $property;
    }

    public function delete(int $id): void
    {
        if (!$this->model->softDelete($id)) {
            Response::error('Property not found', 404);
        }

        $this->redis->del('cache:properties:all');
        $this->redis->del('cache:property:' . $id);
        http_response_code(204);
        exit;
    }
}
