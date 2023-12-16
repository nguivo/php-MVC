<?php
namespace Framework\Routing;

class Router
{

    protected array $routes = [];
    protected array $errorHandlers = [];
    protected Route $current;


    public function add(string $method, string $path, callable $handler): Route
    {
        $route = $this->routes[] = new Route($method, $path, $handler);

        return $route;
    }


    public function dispatch()
    {
        $paths = $this->paths();
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestPath = $_SERVER['REQUEST_URI'] ?? '/';

        // this looks through the defined routes and returns the first route that
        // matches the requested method and path
        $matching = $this->match($requestMethod, $requestPath);

        if($matching) {
            try {
                // this action could throw an exception, so we catch and display a global error page.
                return $matching->dispatch();
            }
            catch(\Throwable $e) {
                return $this->dispatchError();
            }
        }

        // if the path is defined for a different method, we can show a unique error page for it
        if(in_array($requestPath, $paths)) {
            return $this->dispatchNotAllowed();
        }

        return $this->dispatchNotFound();
    }


    public function paths(): array
    {
        $paths = [];
        foreach($this->routes as $route) {
            $paths[] = $route->getPath();
        }
        return $paths;
    }


    private function match(string $method, string $path): ?Route
    {
        foreach($this->routes as $route) {
            if($route->matches($method, $path)) {
                return $route;
            }
        }
        return null;
    }


    public function errorHandler(int $code, callable $handler)
    {
        $this->errorHandlers[$code] = $handler;
    }


    public function dispatchNotAllowed()
    {
        $this->errorHandlers[400] ??= fn() => "not allowed";
        return $this->errorHandlers[400]();
    }


    public function dispatchError()
    {
        // ??= is called the null coalescing operator.
        // it says that the LHS should be set equal to the RHS if the LHS is null or undefined.
        $this->errorHandlers[500] ??= fn() => "server error";
        return $this->errorHandlers[500]();
    }


    public function redirect($path)
    {
        header("Location: {$path}", $replace = true, $code = 301);
        exit;
    }


    public function getCurrent(): ?Route
    {
        return $this->current;
    }


}