# REST HTTP Server

Creating [ReactPHP HTTP Server](https://github.com/reactphp/http) but with REST

## Example

This is an HTTP server which responds with an `hello`
in every request on `<address>/say/hello`.

Every other call will result in an 404 response.

```php

$loop = \React\EventLoop\Factory::create();

$server = new \Legionth\React\Http\Rest\Server();

$server->get('/say/hello', function (\Psr\Http\Message\ServerRequestInterface $request, callable $next) {
    return new \React\Http\Response(200, array(), 'hello');
});

$socket = new \React\Socket\Server(isset($argv[1]) ? $argv[1] : '0.0.0.0:0', $loop);
$server->listen($socket);

$loop->run();

```

Make sure the callback function has 2 parameters.
The first parameter is always the PSR-7 request object.
The second parameter is the next endpoint defined in your API.
If no next function is given it will be
the default function of the server which will respond with will create an 404 error by default.

## Usage

### Server

The `Server` uses the [ReactPHP HTTP Server](https://github.com/reactphp/http) and uses
its internal implemented [middleware](https://github.com/reactphp/http#middleware)

Every endpoint is added as a middleware, therefor a request will pass every function
sequentially.
The second parameter (defined as `$next` in these examples) can be used to pass
the request to following endpoint.

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This will install the latest supported version:

```bash
$ composer require legionth/http-rest:^0.1
```

## License

MIT