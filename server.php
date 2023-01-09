<?php declare(strict_types=1);

$container = require 'bootstrap.php';

$APP_PORT = $container->get('app.port');
$APP_ENV = $container->get('app.environment');
$APP_CACHE = $container->get('app.cache');

$application = $container->get(\DvdSales\Application::class);
error_log(sprintf('started application with mode: %s', $APP_ENV));
error_log(sprintf('started application with cache: %s', $APP_CACHE));

$server = new \Swoole\Http\Server('0.0.0.0', $APP_PORT);
$server->on(
    'start',
    fn () => error_log(sprintf('http server started on port: %s', $APP_PORT))
);
$server->on(
    'request',
    $application->route(...)
);
$server->start();

