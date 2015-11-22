<?php

namespace Aco\CliApp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aco\App\CommandBus;
use Aco\App\Command\DeleteArticleCommand;
use Rhumsaa\Uuid\Uuid;

class DeleteArticleCliCommand extends Command
{
    private $commandBus;

    public function setCommandBus(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
        ->setName('app:delete-article')
        ->setDescription('Delete an article')
        ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'Article UUID'
                )
                ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new DeleteArticleCommand();
        $command->uuid = Uuid::fromString($input->getArgument('uuid'));
        $this->commandBus->handle($command);

        $output->writeln('Article deleted');
    }
}
