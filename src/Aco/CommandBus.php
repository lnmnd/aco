<?php

namespace Aco;

class CommandBus
{
    private $handlers;

    public function __construct($handlers = [])
    {
        foreach ($handlers as $handler) {
            $this->register($handler[0], $handler[1]);
        }
    }

    public function register($commandClassName, Handler $handler)
    {
        $this->handlers[$commandClassName] = $handler;
    }

    public function handle($command)
    {
        return $this->handlers[get_class($command)]->handle($command);
    }
}
