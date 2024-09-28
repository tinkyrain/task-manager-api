<?php

require_once __DIR__ . '/../vendor/autoload.php'; //include autoload
require_once 'db/db.php'; //include db file class

use RedBeanPHP\R;

$dbConnection = DB::getDBConnection();

//include in database
R::setup("pgsql:host=".$dbConnection['host'].";dbname=" . $dbConnection['database'], $dbConnection['user'], $dbConnection['password']);

//check DB
if (!R::testConnection()) {
    die('Failed database connection');
}

//freeze DB
//WARNING! Do not use during development
// R::freeze(true);
