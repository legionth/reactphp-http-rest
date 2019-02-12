<?php

namespace Tests\Legionth\React\Http\Rest;

use Legionth\React\Http\Rest\Server;
use Psr\Http\Message\ServerRequestInterface;

class ServerTest extends TestCase
{
    private $connection;
    private $socket;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder('React\Socket\Connection')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'write',
                    'end',
                    'close',
                    'pause',
                    'resume',
                    'isReadable',
                    'isWritable',
                    'getRemoteAddress',
                    'getLocalAddress',
                    'pipe'
                )
            )
            ->getMock();

        $this->connection->method('isWritable')->willReturn(true);
        $this->connection->method('isReadable')->willReturn(true);


        $this->socket = new \Tests\Legionth\React\Http\Rest\SocketServerStub();
    }

    public function testCreateGetEndpointOnTestServerAndSendRequest()
    {
        $requestAssertion = null;
        $server = new Server();

        $server->get('/user/list', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $server->listen($this->socket);

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("GET http://example.com/user/list HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('GET', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/user/list', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/user/list', $requestAssertion->getUri());
        $this->assertSame('/user/list', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));
    }

    public function testCreatePostEndpointOnTestServerAndSendRequestWithAdditionalParameter()
    {
        $requestAssertion = null;
        $idAssertion = null;

        $server = new Server();

        $server->post('/user/add/:id', function (ServerRequestInterface $request, callable $next, array $paramters) use (&$requestAssertion, &$idAssertion) {
            $requestAssertion = $request;
            $idAssertion = $paramters['id'];
        });

        $server->listen($this->socket);

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("POST http://example.com/user/add/10 HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('POST', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/user/add/10', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/user/add/10', $requestAssertion->getUri());
        $this->assertSame('/user/add/10', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));

        $this->assertEquals('10', $idAssertion);
    }

    public function testCreatePutEndpointOnTestServerAndSendRequestWithParameters()
    {
        $requestAssertion = null;
        $idAssertion = null;
        $nameAssertion = null;

        $server = new Server();

        $server->put('/user/add/:id/group/:name', function (ServerRequestInterface $request, callable $next, array $parameters) use (&$requestAssertion, &$idAssertion, &$nameAssertion) {
            $requestAssertion = $request;
            $idAssertion = $parameters['id'];
            $nameAssertion = $parameters['name'];
        });

        $server->listen($this->socket);

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("PUT http://example.com/user/add/10/group/reactphp HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('PUT', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/user/add/10/group/reactphp', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/user/add/10/group/reactphp', $requestAssertion->getUri());
        $this->assertSame('/user/add/10/group/reactphp', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));

        $this->assertEquals('10', $idAssertion);
        $this->assertEquals('reactphp', $nameAssertion);
    }

    public function testDeleteEndpointOnTestServerAndSendRequest()
    {
        $requestAssertion = null;
        $server = new Server();

        $server->delete('/something/api', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $server->listen($this->socket);

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("DELETE http://example.com/something/api HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('DELETE', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/something/api', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/something/api', $requestAssertion->getUri());
        $this->assertSame('/something/api', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));
    }

    public function testHeadEndpointOnTestServerAndSendRequest()
    {
        $requestAssertion = null;
        $server = new Server();

        $server->head('/something/api', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $server->listen($this->socket);

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("HEAD http://example.com/something/api HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('HEAD', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/something/api', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/something/api', $requestAssertion->getUri());
        $this->assertSame('/something/api', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));
    }

    public function testOptionsEndpointOnTestServerAndSendRequest()
    {
        $requestAssertion = null;
        $server = new Server();

        $server->options('/something/api', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $server->listen($this->socket);

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("OPTIONS http://example.com/something/api HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('OPTIONS', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/something/api', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/something/api', $requestAssertion->getUri());
        $this->assertSame('/something/api', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));
    }

    public function testTraceEndpointOnTestServerAndSendRequest()
    {
        $requestAssertion = null;
        $server = new Server();

        $server->trace('/something/api', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $server->listen($this->socket);

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("TRACE http://example.com/something/api HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('TRACE', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/something/api', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/something/api', $requestAssertion->getUri());
        $this->assertSame('/something/api', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));
    }

    public function testUserDefinedCallbackIsUsed()
    {
        $requestAssertion = null;
        $server = new Server();

        $server->get('/user/list', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $server->listen($this->socket, function (ServerRequestInterface $request) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("GET http://example.com/user/add HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('GET', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/user/add', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/user/add', $requestAssertion->getUri());
        $this->assertSame('/user/add', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));
    }

    public function testUserDefinedCallbackIsNotUsed()
    {
        $requestAssertion = null;
        $server = new Server();

        $server->get('/user/list', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $server->listen($this->socket, function (ServerRequestInterface $request) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("GET http://example.com/user/list HTTP/1.0\r\n\r\n"));

        $this->assertInstanceOf('RingCentral\Psr7\Request', $requestAssertion);
        $this->assertSame('GET', $requestAssertion->getMethod());
        $this->assertSame('http://example.com/user/list', $requestAssertion->getRequestTarget());
        $this->assertEquals('http://example.com/user/list', $requestAssertion->getUri());
        $this->assertSame('/user/list', $requestAssertion->getUri()->getPath());
        $this->assertSame('example.com', $requestAssertion->getHeaderLine('Host'));
    }

    public function testNoSuchCallDefinedResultInNeverCalled()
    {
        $requestAssertion = null;
        $server = new Server();

        $server->get('/user/anotherlist', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $server->get('/user/list', function (ServerRequestInterface $request, callable $next) use (&$requestAssertion) {
            $requestAssertion = $request;
        });

        $this->socket->emit('connection', array($this->connection));
        $this->connection->emit('data', array("GET http://example.com/user/anotherlista HTTP/1.0\r\n\r\n"));

        $this->assertSame(null, $requestAssertion);
    }
}
