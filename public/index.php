<?php

use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;
use App\controllers\TaskController;
use App\helpers\RouterHelper;
use \App\controllers\AuthController;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('BASE_URL', $_ENV['BASE_URL']);
define('HOST_URL', $_ENV['HOST_URL']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_DATABASE', $_ENV['DB_DATABASE']);
define('DB_USERNAME', $_ENV['DB_USERNAME']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('ADMIN_LOGIN', $_ENV['ADMIN_LOGIN']);
define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD']);

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('task.index', \App\helpers\RouterHelper::getUrl(), [TaskController::class, 'index']);
$map->get('task.create', \App\helpers\RouterHelper::getUrl('/task/create'), [TaskController::class, 'create']);
$map->post('task.add', \App\helpers\RouterHelper::getUrl('/task/add'), [TaskController::class, 'add']);

$map->attach('task.', \App\helpers\RouterHelper::getUrl('/task/{id}'), function ($map) {
    $map->get('edit', '/edit', [TaskController::class, 'edit']);
    $map->post('update', '/update', [TaskController::class, 'update']);
    $map->post('delete', '/delete', [TaskController::class, 'delete']);
});

$map->attach('auth.', \App\helpers\RouterHelper::getUrl('/auth'), function ($map) {
    $map->get('auth', '', [AuthController::class, 'auth']);
    $map->post('login', '/login', [AuthController::class, 'login']);
    $map->post('logout', '/logout', [AuthController::class, 'logout']);
});

$matcher = $routerContainer->getMatcher();

$route = $matcher->match($request);

if (!$route) {
    echo "No route found for the request.";
    exit;
}

foreach ($route->attributes as $key => $val) {
    $request = $request->withAttribute($key, $val);
}

$handler = $route->handler;
$response = RouterHelper::getResponseByHandler($handler, $request);

foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
http_response_code($response->getStatusCode());
echo $response->getBody();