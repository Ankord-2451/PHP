<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Request;
use App\Core\Router;
use App\Core\Response;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new Router();

// Public Auth Routes
$router->post('/api/auth/register', 'AuthController@register');
$router->post('/api/auth/login', 'AuthController@login');
$router->post('/api/auth/logout', 'AuthController@logout');
$router->post('/api/auth/forgot-password', 'AuthController@forgotPassword');
$router->post('/api/auth/reset-password', 'AuthController@resetPassword');

// Public Property Routes
$router->get('/api/properties', 'PropertyController@index');
$router->get('/api/properties/:id', 'PropertyController@show');

// Protected Property Routes (admin only)
$router->post('/api/properties', 'PropertyController@store', ['auth', 'admin']);
$router->put('/api/properties/:id', 'PropertyController@update', ['auth', 'admin']);
$router->delete('/api/properties/:id', 'PropertyController@destroy', ['auth', 'admin']);

// Protected Booking Routes
$router->get('/api/bookings', 'BookingController@index', ['auth']);
$router->post('/api/bookings', 'BookingController@store', ['auth']);
$router->put('/api/bookings/:id', 'BookingController@update', ['auth', 'admin']);
$router->delete('/api/bookings/:id', 'BookingController@cancel', ['auth']);

// Admin Routes
$router->get('/api/admin/users', 'AdminController@users', ['auth', 'admin']);
$router->put('/api/admin/users/:id/role', 'AdminController@changeRole', ['auth', 'admin']);
$router->delete('/api/admin/users/:id', 'AdminController@deleteUser', ['auth', 'admin']);
$router->get('/api/admin/bookings', 'AdminController@allBookings', ['auth', 'admin']);
$router->get('/api/admin/stats', 'AdminController@stats', ['auth', 'admin']);
$router->post('/api/admin/users', 'AdminController@createAdminUser', ['auth', 'admin']);

// Stats Routes
$router->get('/api/stats/property/:id', 'StatsController@propertyViews');
$router->get('/api/stats/site', 'StatsController@siteStats', ['auth', 'admin']);

$router->dispatch(new Request());
