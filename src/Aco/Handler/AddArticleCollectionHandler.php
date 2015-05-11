<?php

namespace Aco\Handler;

use Aco\Handler;
use Aco\Command\AddArticleCollectionCommand;

class AddArticleCollectionHandler implements Handler
{
	/**
	 * @see \Aco\Handler::handle()
	 * @param AddArticleCollectionCommand $command
	 */
	public function handle($command)
	{
		echo "handle ".$command->title;
	}
}