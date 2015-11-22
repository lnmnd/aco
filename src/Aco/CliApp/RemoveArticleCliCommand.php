<?php

namespace Aco\CliApp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aco\App\CommandBus;
use Aco\App\Command\RemoveArticleCommand;
use Rhumsaa\Uuid\Uuid;

class RemoveArticleCliCommand extends Command
{
    private $commandBus;

    public function setCommandBus(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
        ->setName('app:remove-article')
        ->setDescription('Remove an article')
        ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'Article UUID'
                )
                ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new RemoveArticleCommand();
        $command->uuid = Uuid::fromString($input->getArgument('uuid'));
        $this->commandBus->handle($command);

        $output->writeln('Article removed');
    }
}
