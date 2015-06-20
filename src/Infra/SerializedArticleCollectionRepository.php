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
use AcoQuery\AcoQuery;
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
            $i++;
        }
        $this->saveAcos($acos);
    }

    // QueryService

    public function getArticleCollections()
    {
        $acos = $this->loadAcos();
        $lacos = [];
        /**
         * @var $aco ArticleCollection
         */
        foreach ($acos as $aco) {
            $lacos[] = new ListAco(
                    $aco->getUuid()->toString(),
                    $aco->getDate(),
                    $aco->getTitle(),
                    $aco->getDescription());
        }
        usort($lacos, function (ListAco $a, ListAco $b) {
            return $a->date < $b->date;
        });

        return $lacos;
    }

    public function getArticleCollection($uuid)
    {
        $acos = $this->loadAcos();
        /**
         * @var $foundAco ArticleCollection
         */
        $foundAco = array_reduce($acos, function ($foundAco, ArticleCollection $aco) use ($uuid) {
            if ($aco->getUuid()->toString() === $uuid) {
                return $aco;
            } else {
                return $foundAco;
            }
        }, false);
        if ($foundAco) {
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
        } else {
            throw new ArticleCollectionNotFoundException();
        }
    }

        public function getTags()
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

            return $tags;
        }

        public function getTagsArticleCollections($tag)
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
                $aco->getDescription());
            }, $filteredAcos);

            return array_values($listAcos);
        }

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
     * @return boolean
     */
    private function fileInitialized()
    {
        return file_exists($this->file);
    }
}
