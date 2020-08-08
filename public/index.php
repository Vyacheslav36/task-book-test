<?php

use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;
use App\controllers\TaskController;
use App\helpers\RouterHelper;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_DATABASE', $_ENV['DB_DATABASE']);
define('DB_USERNAME', $_ENV['DB_USERNAME']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('task.index', '/', [TaskController::class, 'index']);
$map->get('task.create', '/task/create', [TaskController::class, 'create']);
$map->post('task.add', '/task/add', [TaskController::class, 'add']);

$map->attach('task.', '/task/{id}', function ($map) {
    $map->get('edit', '/edit', [TaskController::class, 'edit']);
    $map->post('update', '/update', [TaskController::class, 'update']);
    $map->post('delete', '/delete', [TaskController::class, 'delete']);
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