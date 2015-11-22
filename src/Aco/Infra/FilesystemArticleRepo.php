<?php

namespace Aco\Infra;

use Aco\Domain\Aco\ArticleRepo;
use Aco\Domain\Aco\Article;
use Aco\Domain\Aco\Exception\ArticleDoesNotExistException;
use Rhumsaa\Uuid\Uuid;

class FilesystemArticleRepo implements ArticleRepo
{
    private $file;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function store(Article $article)
    {
        $xs = $this->loadArticles();
        // TODO check if exists
        /* @var Article $x */
        $i = 0;
        foreach ($xs as $x) {
            if ($x->isEqual($article)) {
                $xs[$i] = $article;
                $this->saveArticles($xs);

                return;
            }
            ++$i;
        }
        $xs[] = $article;
        $this->saveArticles($xs);
    }

    public function remove(Article $article)
    {
        var_dump($article->getUuid()->toString());
        $xs = $this->loadArticles();
        $i = 0;
        foreach ($xs as $x) {
            if ($x->getUuid()->equals($article->getUuid())) {
                unset($xs[$i]);
                $xs = array_values($xs);
                break;
            }
            ++$i;
        }
        $this->saveArticles($xs);
    }

    public function find(Uuid $uuid)
    {
        $xs = $this->loadArticles();
        /** @var Article $x */
        foreach ($xs as $x) {
            if ($x->getUuid()->equals($uuid)) {
                return $x;
            }
        }

        throw new ArticleDoesNotExistException();
    }

    public function findAll()
    {
        return $this->loadArticles();
    }

    private function loadArticles()
    {
        if ($this->fileInitialized()) {
            $contents = file_get_contents($this->file);
            $xs = unserialize($contents);
        } else {
            $xs = [];
        }

        return $xs;
    }

    private function saveArticles($articles)
    {
        file_put_contents($this->file, serialize($articles));
    }

    private function fileInitialized()
    {
        return file_exists($this->file);
    }
}