<?php

use Framework\Routing\Router;

require_once dirname(__DIR__).'/vendor/autoload.php';

    $router = new Router();
    $routes = require_once dirname(__DIR__).'/app/routes.php';

    $routes($router);

    print $router->dispatch();

    echo '<br>well done';