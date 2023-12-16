<?php

use Framework\Routing\Router;

return function (Router $router) {
    $router->add('GET', '/', fn() => 'hello world');

    $router->add('GET', '/old-home', fn() => $router->redirect('/'));

    $router->add('GET', '/has-server-error', fn() => throw new Exception());

    $router->add('GET', '/has-validation-error', fn() => $router->dispatchNotAllowed());

    $router->errorHandler(404, fn() => 'whoops!');

    $router->add('GET', 'products/view/{products}',
        function () use ($router) {
            $parameters = $router->getCurrent()->getParameters();
            return "product is {$parameters['product']}";
        }
    );

    $router->add('GET', '/services/view/{service?}',
        function () use ($router) {
            $parameters = $router->getCurrent()->getParameters();
            if(empty($parameters['service'])) {
                return 'all service';
            }
            return "Service is {$parameters['service']}";
        }
    );

};