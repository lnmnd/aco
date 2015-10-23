<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aco\CommandBus;
use Aco\Command\RemoveArticleCollectionCommand;

class RemoveCommand extends Command
{
    private $commandBus;

    public function setCommandBus(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
        ->setName('aco:rm')
        ->setDescription('Remove an article collection')
        ->addArgument(
            'uuid',
            InputArgument::REQUIRED,
            'Collection UUID'
        )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uuid = $input->getArgument('uuid');

        $this->commandBus->handle(
            new RemoveArticleCollectionCommand($uuid)
        );

        $output->writeln('Removed article collection with uuid '.$uuid);
    }
}
