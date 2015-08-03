<?php

namespace Aco;

class DummyCommand
{
}

class DummyHandler implements Handler
{
    public function handle($command)
    {

    }
}

class CommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        new CommandBus();
    }

    public function testConstructWithHandlers()
    {
        $handlers = [['DummyCommand', new DummyHandler()]];
        new CommandBus($handlers);
    }
}
