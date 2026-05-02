<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\RedisClient;
use App\Core\Response;
use App\Core\Validator;
use App\Helpers\JwtHelper;
use App\Models\UserModel;

/**
 * Handles user registration, login, logout, and password recovery.
 */
class AuthService
{
    private UserModel $model;
    private $redis;
    private int $ttl;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->redis = RedisClient::getInstance();
        $this->ttl = (int)(getenv('JWT_TTL') ?: 86400);
    }

    public function register(array $data): array
    {
        $validator = new Validator($data);
        $validator->required('name')->minLength('name', 2)->maxLength('name', 100)
            ->required('email')->email('email')
            ->required('password')->minLength('password', 8);

        if ($validator->fails()) {
            Response::error($validator->errors(), 422);
        }

        if ($this->model->findByEmail($data['email']) !== null) {
            Response::error(['email' => 'Email is already registered.'], 422);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'role' => 'user',
        ]);

        $token = JwtHelper::encode(['user_id' => (int)$user['id'], 'role' => $user['role'], 'exp' => time() + $this->ttl]);
        $this->redis->setex('session:' . $user['id'], $this->ttl, $token);

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $data): array
    {
        $validator = new Validator($data);
        $validator->required('email')->email('email')
            ->required('password')->minLength('password', 8);

        if ($validator->fails()) {
            Response::error($validator->errors(), 422);
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $rateKey = 'rate_limit:login:' . $ip;
        $failedAttempts = $this->redis->get($rateKey);

        if ($failedAttempts !== null && (int)$failedAttempts >= 5) {
            Response::error('Too many login attempts. Try again later.', 429);
        }

        $user = $this->model->findByEmail($data['email']);
        if ($user === null || !password_verify($data['password'], $user['password'])) {
            $this->redis->incr($rateKey);
            $this->redis->expire($rateKey, 300);
            Response::error('Invalid email or password.', 401);
        }

        $this->redis->del($rateKey);
        $token = JwtHelper::encode(['user_id' => (int)$user['id'], 'role' => $user['role'], 'exp' => time() + $this->ttl]);
        $this->redis->setex('session:' . $user['id'], $this->ttl, $token);

        return ['user' => $this->model->findById((int)$user['id']), 'token' => $token];
    }

    public function logout(string $token): void
    {
        $payload = JwtHelper::decode($token);
        if (!isset($payload['user_id'])) {
            Response::error('Unauthorized', 401);
        }

        $this->redis->del('session:' . $payload['user_id']);
    }

    public function forgotPassword(array $data): void
    {
        Response::error('Password recovery is not implemented.', 501);
    }

    public function resetPassword(array $data): void
    {
        Response::error('Password reset is not implemented.', 501);
    }
}
