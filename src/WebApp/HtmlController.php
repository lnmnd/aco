<?php

namespace WebApp;

use AcoQuery\QueryService;
use Aco\CommandBus;
use Aco\Command\AddArticleCollectionCommand;
use AcoQuery\Exception\ArticleCollectionNotFoundException;

class HtmlController
{
    private $mustache;
    private $commandBus;
    private $queryService;
    private $blockContent;

    public function __construct(\Mustache_Engine $mustache, CommandBus $commandBus, QueryService $queryService)
    {
        $this->mustache = $mustache;
        $this->commandBus = $commandBus;
        $this->queryService = $queryService;

        $this->blockContent = function ($tmpl, $helper) {
            $content = $helper->render($tmpl);

            return $this->render(
                'base.html', ['content' => $content]
            );
        };
    }

    public function getArticleCollections()
    {
        echo $this->renderContent(
            'index.html',
            ['acos' => $this->queryService->getArticleCollections()]
        );
    }

    public function getArticleCollection($uuid)
    {
        try {
            echo $this->renderContent(
                'aco.html',
                ['aco' => $this->queryService->getArticleCollection($uuid)]);
        } catch (ArticleCollectionNotFoundException $e) {
            header('HTTP/1.0. 404 Not Found');

            echo $this->renderContent('404.html', []);
        }
    }


    private function render($template, $data)
    {
        return $this->mustache->render($template, $data);
    }

    private function renderContent($template, $data)
    {
        $data['block_content'] = $this->blockContent;

        return $this->render($template, $data);
    }
}
