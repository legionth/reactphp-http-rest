# REST HTTP Server

Creating [ReactPHP HTTP Server](https://github.com/reactphp/http) but with REST


**Table of Contents**
* [Example](#example)
* [Usage](#usage)
  * [Server](#server)
  * [Dynamic Values](#dynamic-values)
  * [Default Callback](#default-callback)
  * [Parameter Placeholder](#parameter-placholder)
* [Install](#install)
* [License](#license)

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

### Dynamic Values

To add dynamic values in the REST API definition the operator `:` can be used

```php
$server->post('/say/:word', function (\Psr\Http\Message\ServerRequestInterface $request, callable $next, array $parameters) {
    $word = $parameters['word'];

    return new \React\Http\Response(200, array(), 'You said: ' . $word);
});
```

Now a HTTP client can call the address e.g. `http://localhost:8080/say/hello`.
The key `word` and value `hello` will be stored in the third
parameter of the callback function.

There is no type check her that can validate which API should be used.
`/say/:word` and`/say/:number` would be the same. In this case the order of your API
definition matters.

### Default Callback

A default callback can be defined in the `listen` method.
This method will be used if no definition can be found.

By default this library will respond with an `404` HTTP response.

### Parameter Placeholder

As seen in the previous chapter you can use the `:` to mark
dynamic values.
Instead of using this strategy, to mark dynamic parameters,
this library supports additional strategies via different classes:

* `/to/path/:paramter` - `Legionth\React\Http\Rest\Paramaters\Label\Colon`
* `/to/path/[paramter]` - `Legionth\React\Http\Rest\Paramaters\Label\CurlyBracket`
* `/to/path/{paramter}` - `Legionth\React\Http\Rest\Paramaters\Label\SquareBrackets` 

Checkout the examples for more information.

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This will install the latest supported version:

```bash
$ composer require legionth/http-rest:^0.2
```

## License

MIT
