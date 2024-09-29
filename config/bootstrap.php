<?php

use App\Handlers\Errors\JsonErrorHandler;
use Slim\App;
use Slim\Factory\AppFactory;

// add autoload
require __DIR__ . '/../vendor/autoload.php';

// Function for setup app
function createApp(): App
{
    $app = AppFactory::create();

    //region errors middleware
    $app->addErrorMiddleware(true, true, true)
        ->setDefaultErrorHandler(new JsonErrorHandler());
    //endregion

    //region RedBeanOrm
    require __DIR__ . '/../config/orm/rb.php';
    //endregion

    //region routes
    (require __DIR__ . '/../routes/routes.php')($app);
    //endregion

    return $app;
}