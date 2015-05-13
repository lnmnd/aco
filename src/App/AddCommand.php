<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends Command
{
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

		$output->writeln('added');
	}
}