<?php

use Charcoal\App\App;
use Charcoal\App\AppConfig;
use Charcoal\App\AppContainer;

/* If using PHP's built-in server, return false to skip existing files on the filesystem. */
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
    if (is_file($file)) {
        return false;
    }
}

$basePath = dirname(__DIR__);

require $basePath . '/vendor/autoload.php';

/* Import the application's settings */
$appConfig = new AppConfig([
    'base_path'   => $basePath,
    'public_path' => __DIR__,
]);
$appConfig->addFile($basePath . '/config/config.php');

/* Build the DI container */
$container = new AppContainer([
    'config' => $appConfig,
    'settings' => [
        'displayErrorDetails' => $appConfig['debug'],
    ],
]);

/* Instantiate a Charcoal~Slim application and run */
$app = App::instance($container);
$app->run();
