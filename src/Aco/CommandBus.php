<?php

namespace Aco;

class CommandBus
{
	private $handlers;
	
	public function register($commandClassName, Handler $handler)
	{
		$this->handlers[$commandClassName] = $handler;
	}
	
	public function handle($command)
	{
		$this->handlers[get_class($command)]->handle($command);
	}
}