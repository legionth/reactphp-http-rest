<?php

namespace Tests\Legionth\React\Http\Rest;

use Evenement\EventEmitter;
use React\Socket\ServerInterface;

/**
 * This class is a copy of https://github.com/reactphp/http/blob/master/tests/SocketServerStub.php
 */
class SocketServerStub extends EventEmitter implements ServerInterface
{
    public function getAddress()
    {
        return '127.0.0.1:8080';
    }
    public function close()
    {
        // NO-OP
    }
    public function pause()
    {
        // NO-OP
    }
    public function resume()
    {
        // NO-OP
    }
}