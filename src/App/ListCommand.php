<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AcoQuery\QueryService;
use AcoQuery\ListAco;

class ListCommand extends Command
{
    private $queryService;

    public function setQueryService(QueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    protected function configure()
    {
        $this
        ->setName('aco:list')
        ->setDescription('Lists all article collections')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Article collections:');
        $acos = $this->queryService->getArticleCollections();
        /**
         * @var $aco ListAco
         */
        foreach ($acos as $aco) {
            $txt = '- '.$aco->uuid.' ['.$aco->date->format('i:s').'] '.$aco->title;
            $output->writeln($txt);
        }
    }
}
