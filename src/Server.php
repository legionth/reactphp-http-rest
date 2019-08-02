<?php

namespace Legionth\React\Http\Rest;

use Legionth\React\Http\Rest\Paramaters\Label\Colon;
use Legionth\React\Http\Rest\Paramaters\Label\Strategy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Socket\ServerInterface;
use RingCentral\Psr7\Response;

class Server
{

    const DOUBLE_POINT = ':';
    const CURLY_BRACKETS = '{}';
    const SQUARE_BRACKETS = '[]';

    /**
     * @var array
     */
    private $functions;

    /**
     * @var Strategy
     */
    private $strategy;

    /**
     * @param ServerInterface $socket
     * @param callable|null $callback
     * @param Strategy|string $strategy
     */
    public function listen(ServerInterface $socket, callable $callback = null, Strategy $strategy = null)
    {
        $middleWareFunctions = array();

        if ($strategy === null) {
            $strategy = new Colon();
        }

        $this->strategy = $strategy;

        foreach ($this->functions as $httpMethod => $pathFunctionArray) {
            foreach ($pathFunctionArray as $path => $function) {
                $middleWareFunctions[] = $this->createRestfulFunction($httpMethod, $path, $function);
            }
        }

        if (null === $callback) {
            $callback = function (ServerRequestInterface $request) {
                return new Response(404);
            };
        }

        $middleWareFunctions[] = $callback;

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

                $countRequestPathEntries = count($requestPathArray);
                if ($countRequestPathEntries !== count($pathArray)) {
                    return $next($request);
                }

                if ($requestPath === $path) {
                    return $function($request, $next);
                }

                if (false === strpos($path, $this->strategy->getFirstIdentifier())) {
                    return $next($request);
                }

                $argument = $this->strategy->extractParametersFromPath($pathArray, $requestPathArray);

                return $function($request, $next, $argument);
            }

            return $next($request);
        };
    }
}

