<?php

namespace DvdSales;

use DI\Container;
use Doctrine\DBAL\Connection;
use FastRoute\Dispatcher;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
class Application
{
    public function __construct(
        private Container $container,
        private Dispatcher $dispatcher
    ) {}
    
    public function route(Request $request, Response $response)
    {
        $httpMethod = $request->getMethod();
        $uri = $request->server['request_uri'];
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
    
        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $response->status(200);
                
                $route = $this->container->make($handler);
                $route->handle($request, $response, ...$vars);
                break;
    
            case Dispatcher::NOT_FOUND:
                $response->status(404);
                break;
    
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $response->header('Allow', implode(', ', $allowedMethods));
                $response->status(405);
                break;
    
            default:
                $response->status(500);
        }
    
        error_log(sprintf('%s %s', $httpMethod, $uri));
    }

}
