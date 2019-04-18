<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$server = new \Legionth\React\Http\Rest\Server();

$server->get('/say/hello', function (\Psr\Http\Message\ServerRequestInterface $request, callable $next) {
    return new \React\Http\Response(200, array(), 'hello');
});

$server->post('/say/{word}', function (\Psr\Http\Message\ServerRequestInterface $request, callable $next, array $arguments) {
    $word = $arguments['word'];

    return new \React\Http\Response(200, array(), 'You said: ' . $word);
});

$socket = new \React\Socket\Server(isset($argv[1]) ? $argv[1] : '0.0.0.0:0', $loop);

$server->listen($socket, null, new \Legionth\React\Http\Rest\Paramaters\Label\CurlyBrackets());

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
