<?php

use Charcoal\App\App;
use Charcoal\App\AppConfig;
use Charcoal\App\AppContainer;
use Nyholm\Psr7\Response;
use Slim\Factory\ServerRequestCreatorFactory;

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

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();
$request = $request->withUri($request->getUri()->withPort(null));

/* Build the DI container */
$container = new AppContainer([
    'config' => $appConfig,
    'request' => $request,
    'settings' => [
        'displayErrorDetails' => $appConfig['debug'],
    ],
]);

/* Instantiate a Charcoal~Slim application and run */
/** @var Slim\App $app */
$response = new Response();
$app = App::instance($container);
$app->setConfig($appConfig);
$app->setBasePath('');

$response = $app->getResponseFactory()->createResponse();
//$app->addRoutingMiddleware();

$app->run($request, $response);

//if (!$silent) {
    //(new \Slim\ResponseEmitter())->emit($response);
//}
