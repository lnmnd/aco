<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Aco\CommandBus;
use Aco\Command\AddArticleCollectionCommand;

class AddCommand extends Command
{
	private $commandBus;
	
	public function setCommandBus(CommandBus $commandBus)
	{
		$this->commandBus = $commandBus;
	}
	
	protected function configure()
	{
		$this
		->setName('aco:add')
		->setDescription('Add an article collection')
		->addArgument(
				'title',
				InputArgument::REQUIRED,
				'Collection title'
		)
		->addArgument(
				'description',
				InputArgument::REQUIRED,
				'Collection description'
		)
		->addArgument(
				'urls',
				InputArgument::REQUIRED | InputArgument::IS_ARRAY,
				'List of URLs'
		)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$title = $input->getArgument('title');
		$description = $input->getArgument('description');
		$urls = $input->getArgument('urls');
		
		$output->writeln('title: '.$title);
		$output->writeln('description: '.$description);
		$output->writeln('urls:');
		foreach ($urls as $url) {
			$output->writeln('  - '.$url);
		}
		
		$uuid = $this->commandBus->handle(
				new AddArticleCollectionCommand($title, $description, $urls)
		);
		
		$output->writeln('Added with uuid '.$uuid);
	}
}