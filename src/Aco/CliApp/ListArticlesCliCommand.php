<?php

namespace Aco\CliApp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aco\Domain\Aco\ArticleRepo;
use Aco\Domain\Aco\Article;

class ListArticlesCliCommand extends Command
{
    private $repo;

    public function setRepo(ArticleRepo $repo)
    {
        $this->repo = $repo;
    }

    protected function configure()
    {
        $this
        ->setName('app:list-articles')
        ->setDescription('Articles list')
    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Articles:');
        $xs = $this->repo->findArticles();

        /*
         * @var Article $x
         */
        foreach ($xs as $x) {
            $txt = '- '.$x->uuid.' '.$x->title;
            $output->writeln($txt);
        }
    }
}
