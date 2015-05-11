<?php

use Aco\CommandBus;
use Aco\Handler\AddArticleCollectionHandler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\Command\Aco\Command;
use Aco\DateTimeGetter;

class DummyDateTimeGetter implements DateTimeGetter
{
	public function now()
	{
		return null;
	}
}

class AddArticleCollectionTest extends \PHPUnit_Framework_TestCase {
	public function testAdd()
	{
		$dtg = new DummyDateTimeGetter();
		$cb = new CommandBus();
		$cb->register('Aco\Command\AddArticleCollectionCommand', new AddArticleCollectionHandler($dtg));
		
		$c = new AddArticleCollectionCommand('title', 'description');
		$id = $cb->handle($c);
		
		$this->assertEquals('uuid', $id);
	}
}
