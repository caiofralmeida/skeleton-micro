<?php

require __DIR__ . '/../init.php';

require __DIR__ . '/../vendor/autoload.php';

use App\MicroBuilder;

$appBuilder = new MicroBuilder();
$appBuilder->registerServices();
$appBuilder->registerConnections();

/*
$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'This is crazy, but this page was not found!';
});

$app->handle();
*/
