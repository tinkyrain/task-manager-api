<?php

use App\Handlers\Errors\JsonErrorHandler;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php'; //include autoload

//create app
$app = AppFactory::create();

//region Middlewares
$app->addErrorMiddleware(true, true, true)
    ->setDefaultErrorHandler(new JsonErrorHandler());
//endregion

//region ReadBean ORM
require __DIR__ . '/../config/orm/rb.php';
//endregion

//region Routes
(require __DIR__ . '/../routes/routes.php')($app);
//endregion

$app->run();