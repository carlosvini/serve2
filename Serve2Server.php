<?php

use Evenement\EventEmitter;
use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\StreamingServer;
use React\Socket\ServerInterface;

final class Serve2Server extends EventEmitter
{
    /**
     * @var StreamingServer
     */
    private $streamingServer;

    /**
     * @see StreamingServer::__construct()
     */
    public function __construct($requestHandler)
    {
        if (!\is_callable($requestHandler) && !\is_array($requestHandler)) {
            throw new \InvalidArgumentException('Invalid request handler given');
        }

        $middleware = array();
        $middleware[] = new RequestBodyBufferMiddleware();

        if (\is_callable($requestHandler)) {
            $middleware[] = $requestHandler;
        } else {
            $middleware = \array_merge($middleware, $requestHandler);
        }

        $this->streamingServer = new StreamingServer($middleware);

        $that = $this;
        $this->streamingServer->on('error', function ($error) use ($that) {
            $that->emit('error', array($error));
        });
    }

    /**
     * @see StreamingServer::listen()
     */
    public function listen(ServerInterface $server)
    {
        $this->streamingServer->listen($server);
    }
}
