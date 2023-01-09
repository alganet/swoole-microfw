<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config/config.php');
$builder->useAutowiring(true);

if ($_ENV['app_cache'] ?? 'off' === 'on') {
    $builder->enableCompilation(__DIR__ . '/cache/di');
}

return $builder->build();
