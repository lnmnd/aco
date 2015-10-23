<?php

namespace Infra;

use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;
use Aco\Article;
use Aco\Exception\DoesNotExistException;
use AcoQuery\QueryService;
use AcoQuery\ListAco;
use AcoQuery\Exception\ArticleCollectionNotFoundException;
use AcoQuery\FullAco;
use AcoQuery\FullArticle;
use Rhumsaa\Uuid\Uuid;

class SerializedArticleCollectionRepository implements ArticleCollectionRepository, QueryService
{
    private $file;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    // ArticleCollectionRepository

    public function add(ArticleCollection $articleCollection)
    {
        $acos = $this->loadAcos();
        $acos[] = $articleCollection;
        $this->saveAcos($acos);
    }

    public function get(Uuid $uuid)
    {
        $acos = $this->loadAcos();
        foreach ($acos as $aco) {
            if ($aco->getUuid()->equals($uuid)) {
                return $aco;
            }
        }
        throw new DoesNotExistException();
    }

    public function remove(ArticleCollection $articleCollection)
    {
        $acos = $this->loadAcos();
        $i = 0;
        foreach ($acos as $aco) {
            if ($aco->getUuid()->equals($articleCollection->getUuid())) {
                unset($acos[$i]);
                $acos = array_values($acos);
            }
            ++$i;
        }
        $this->saveAcos($acos);
    }

    // QueryService

    public function getArticleCollections($offset = 0, $limit = 0)
    {
        $acos = $this->loadAcos();
        $lacos = [];
        /*
         * @var ArticleCollection
         */
        foreach ($acos as $aco) {
            $lacos[] = new ListAco(
                $aco->getUuid()->toString(),
                $aco->getDate(),
                $aco->getTitle(),
                $aco->getDescription()
            );
        }
        usort($lacos, function (ListAco $a, ListAco $b) {
            return $a->date < $b->date;
        });

        return ($limit === 0) ? $lacos : array_slice($lacos, $offset, $limit);
    }

    public function getArticleCollection($uuid)
    {
        $acos = $this->loadAcos();
        /*
         * @var ArticleCollection
         */
        $foundAco = array_reduce($acos, function ($foundAco, ArticleCollection $aco) use ($uuid) {
            if ($aco->getUuid()->toString() === $uuid) {
                return $aco;
            }

            return $foundAco;
        }, false);

        if (!$foundAco) {
            throw new ArticleCollectionNotFoundException();
        }

        $articles = array_map(function (Article $x) {
            return new FullArticle($x->getUrl()->getUrl(), $x->getTitle(), $x->getOriginalContent(), $x->getContent());
        }, $foundAco->getArticles());

        return new FullAco(
            $foundAco->getUuid()->toString(),
            $foundAco->getDate(),
            $foundAco->getTitle(),
            $foundAco->getDescription(),
            $foundAco->getTags(),
            $articles
        );
    }

    public function getTags($offset = 0, $limit = 0)
    {
        $acos = $this->loadAcos();
        $tags = array_reduce($acos, function ($tags, ArticleCollection $aco) {
            $acoTags = $aco->getTags();
            foreach ($acoTags as $tag) {
                if (!in_array($tag, $tags)) {
                    $tags[] = $tag;
                }
            }

            return $tags;
        }, []);
        usort($tags, function ($a, $b) {
            return $a > $b;
        });

        return ($limit === 0) ? $tags : array_slice($tags, $offset, $limit);
    }

    public function getTagsArticleCollections($tag, $offset = 0, $limit = 0)
    {
        $acos = $this->loadAcos();

        $filteredAcos = array_filter($acos, function (ArticleCollection $aco) use ($tag) {
                return in_array($tag, $aco->getTags());
        });

        $listAcos = array_map(function (ArticleCollection  $aco) {
            return new ListAco(
                $aco->getUuid()->toString(),
                $aco->getDate(),
                $aco->getTitle(),
                $aco->getDescription()
            );
        }, $filteredAcos);

        $acos = array_values($listAcos);

        return ($limit === 0) ? $acos : array_slice($acos, $offset, $limit);
    }

    public function getArticles($offset = 0, $limit = 0)
    {
        $articles = [];
        $acos = array_reverse($this->loadAcos());
        foreach ($acos as $aco) {
            foreach ($aco->getArticles() as $x) {
                $articles[] = new FullArticle(
                    $x->getUrl()->getUrl(),
                    $x->getTitle(),
                    $x->getOriginalContent(),
                    $x->getContent()
                );
            }
        }

        return ($limit === 0) ? $articles : array_slice($articles, $offset, $limit);
    }

    /**
     * @return ArticleCollection[]
     */
    private function loadAcos()
    {
        if ($this->fileInitialized()) {
            $contents = file_get_contents($this->file);
            $acos = unserialize($contents);
        } else {
            $acos = [];
        }

        return $acos;
    }

    private function saveAcos($acos)
    {
        file_put_contents($this->file, serialize($acos));
    }

    /**
     * @return bool
     */
    private function fileInitialized()
    {
        return file_exists($this->file);
    }
}
