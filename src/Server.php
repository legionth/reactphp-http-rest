<?php

namespace Legionth\React\Http\Rest;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Socket\ServerInterface;
use RingCentral\Psr7\Response;

class Server
{
    /**
     * @var array
     */
    private $functions;

    /**
     * @param ServerInterface $socket
     */
    public function listen(ServerInterface $socket)
    {
        $middleWareFunctions = array();

        foreach ($this->functions as $httpMethod => $pathFunctionArray) {
            foreach ($pathFunctionArray as $path => $function) {
                $middleWareFunctions[] = $this->createRestfulFunction($httpMethod, $path, $function);
            }
        }

        $middleWareFunctions[] = function (ServerRequestInterface $request) {
            return new Response(404);
        };

        $httpServer = new \React\Http\Server($middleWareFunctions);

        $httpServer->listen($socket);
    }

    public function get(string $path, callable $callable)
    {
        $this->functions['get'][$path] = $callable;
    }

    public function post(string $path, callable $callable)
    {
        $this->functions['post'][$path] = $callable;
    }

    public function put(string $path, callable $callable)
    {
        $this->functions['put'][$path] = $callable;
    }

    public function delete(string $path, callable $callable)
    {
        $this->functions['delete'][$path] = $callable;
    }

    public function head(string $path, callable $callable)
    {
        $this->functions['head'][$path] = $callable;
    }

    public function options(string $path, callable $callable)
    {
        $this->functions['options'][$path] = $callable;
    }

    public function trace(string $path, callable $callable)
    {
        $this->functions['trace'][$path] = $callable;
    }

    /**
     * @param $httpMethod
     * @param $path
     * @param $function
     * @return \Closure
     */
    private function createRestfulFunction($httpMethod, $path, $function)
    {
        return function (RequestInterface $request, callable $next) use ($httpMethod, $path, $function) {
            if ($request->getMethod() === strtoupper($httpMethod)) {
                $requestPath = $request->getUri()->getPath();
                $requestPathArray = explode('/', $requestPath);
                $pathArray = explode('/', $path);

                if (count($requestPathArray) !== count($pathArray)) {
                    return $next($request);
                }

                $queryParams = $request->getQueryParams();

                foreach ($pathArray as $id => $valueName) {
                    $position = strpos($valueName, ':');
                    if (0 === $position) {
                        $valueName = substr($valueName, 1);
                        $queryParams[$valueName] = $requestPathArray[$id];
                    }
                }

                $request = $request->withQueryParams($queryParams);

                return $function($request, $next);
            }

            return $next($request);
        };
    }
}

