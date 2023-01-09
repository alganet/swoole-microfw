<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use DvdSales\Application;
use DvdSales\Routes as r;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\cachedDispatcher;

return [
    /* Environment configuration */
    'app.environment' => 'dev',
    'app.port' => intval($_ENV['APP_PORT']),
    'app.cache' => $_ENV['APP_CACHE'],

    /* The main() */
    Application::class => DI\autowire(),

    /* FastRoute DI */
    Dispatcher::class => fn(\DI\Container $c)
        => cachedDispatcher(
            function (RouteCollector $r) {
                $r->addRoute('GET', '/actors', r\GetActors::class);
                $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
                $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
            },
            [
                'cacheFile' => __DIR__ . '/../cache/routes-cache.php',
                'cacheDisabled' => !$c->get('app.cache') === 'on'
            ]
        ),

    /* Doctrine DBAL DI */
    Connection::class => fn()
        => DriverManager::getConnection([
            'dbname' => $_ENV['MYSQL_DATABASE'],
            'user' => 'root',
            'password' => $_ENV['MYSQL_ROOT_PASSWORD'],
            'host' => 'database', # name of the container
            'port' => intval($_ENV['MYSQL_PORT']),
            'driver' => 'pdo_mysql',
        ]),

    /* Doctrine Migrations DI */
    DependencyFactory::class => fn(DI\Container $c)
        => DependencyFactory::fromConnection(
            new PhpFile(__DIR__ . '/config/migrations.php'),
            new ExistingConnection($c->get(Connection::class))
        ),

    /* This is requested internally to warmup/enable the cache */
    'CacheWarmup'
        => function (\DI\Container $c) {
            if ($c->get('app.cache') === 'on') {
                $c->get(Dispatcher::class);
            } else {
                error_log("cache is off (which is good for development)");
            }
        }
];