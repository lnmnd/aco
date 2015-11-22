<?php

namespace WebApp;

use AcoQuery\QueryService;
use AcoQuery\Exception\ArticleNotFoundException;

class HtmlController
{
    private $mustache;
    private $queryService;
    private $blockContent;

    public function __construct(\Mustache_Engine $mustache, QueryService $queryService)
    {
        $this->mustache = $mustache;
        $this->queryService = $queryService;

        $this->blockContent = function ($tmpl, $helper) {
            $content = $helper->render($tmpl);

            return $this->render(
                'base.html', ['content' => $content]
            );
        };
    }

    public function getArticles()
    {
        echo $this->renderContent(
            'index.html',
            ['articles' => $this->queryService->findArticles()]
        );
    }

    public function getArticle($uuid)
    {
        try {
            echo $this->renderContent(
                'article.html',
                ['article' => $this->queryService->findArticle($uuid)]);
        } catch (ArticleNotFoundException $e) {
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
