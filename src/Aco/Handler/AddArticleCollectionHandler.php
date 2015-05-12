<?php

namespace Aco\Handler;

use Aco\Handler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\DateTimeGetter;

class AddArticleCollectionHandler implements Handler
{
	/**
	 * @var DateTimeGetter
	 */
	private $dateTimeGetter;
	
	public function __construct(DateTimeGetter $dateTimeGetter)
	{
		$this->dateTimeGetter = $dateTimeGetter;
	}
	
	/**
	 * @see \Aco\Handler::handle()
	 * @param AddArticleCollectionCommand $command
	 */
	public function handle($command)
	{
		echo "handle ".$command->title;
		// just call
		$this->dateTimeGetter->now();
		return 'uuid';
	}
}