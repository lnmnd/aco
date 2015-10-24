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

            $this->render(
                'base.html', ['content' => $content]
            );
        };
    }

    public function addArticleCollection()
    {
        $error = false;
        if (isset($_POST['title'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $urls = explode(',', $_POST['urls']);
            $tags = explode(',', $_POST['tags']);
            // empty
            if ((count($tags) === 1) && $tags[0] === '') {
                $tags = [];
            }

            try {
                $uuid = $this->commandBus->handle(
                    new AddArticleCollectionCommand($title, $description, $urls, $tags)
                );
                header('Location: /article-collections/'.$uuid);

                return;
            } catch (\Exception $e) {
                $error = true;
            }
        }
        $this->renderContent('add.html', ['error' => $error]);
    }

    public function getArticleCollections()
    {
        $this->renderContent(
            'index.html',
            ['acos' => $this->queryService->getArticleCollections()]
        );
    }

    public function getArticleCollection($uuid)
    {
        try {
            $this->renderContent(
                'aco.html',
                ['aco' => $this->queryService->getArticleCollection($uuid)]);
        } catch (ArticleCollectionNotFoundException $e) {
            header('HTTP/1.0. 404 Not Found');

            $this->renderContent('404.html', []);
        }
    }

    public function getTags()
    {
        $this->renderContent('tags.html',
                             ['tags' => $this->queryService->getTags()]);
    }

    public function getTagsArticleCollections($tag)
    {
        $this->renderContent('tags-acos.html', ['tag' => $tag,
                                                'acos' => $this->queryService->getTagsArticleCollections($tag), ]);
    }

    private function render($template, $data)
    {
        echo $this->mustache->render($template, $data);
    }

    private function renderContent($template, $data)
    {
        $data['block_content'] = $this->blockContent;

        return $this->render($template, $data);
    }
}
