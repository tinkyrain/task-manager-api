<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php'; //include autoload

//create app
$app = AppFactory::create();

//region Middlewares
$app->addErrorMiddleware(true, true, true); //enabled errors
//endregion

//region ReadBean ORM
require __DIR__ . '/../config/rb.php';
//endregion

//region Routes
(require __DIR__ . '/../routes/routes.php')($app);
//endregion

$app->run();