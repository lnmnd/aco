<?php

namespace Aco\CliApp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aco\App\CommandBus;
use Aco\App\Command\AddArticleCommand;

class AddArticleCliCommand extends Command
{
    private $commandBus;

    public function setCommandBus(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
        ->setName('app:add-article')
        ->setDescription('Add an article')
        ->addArgument(
            'title',
            InputArgument::REQUIRED,
            'Collection title'
        )
        ->addArgument(
            'url',
            InputArgument::REQUIRED,
            'URL of the article'
        )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('title');
        $url = $input->getArgument('url');
        $command = new AddArticleCommand();
        $command->title = $title;
        $command->url = $url;
        $uuid = $this->commandBus->handle($command);

        $output->writeln('Added with uuid '.$uuid);
    }
}
