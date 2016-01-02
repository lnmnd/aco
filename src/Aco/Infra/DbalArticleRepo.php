<?php

namespace Aco\Infra;

use Aco\Domain\Aco\ArticleRepo;
use Aco\Domain\Aco\Article;
use Aco\Domain\Aco\ArticleSource;
use Aco\Domain\Aco\Exception\ArticleDoesNotExistException;
use Aco\Domain\Aco\Url;
use AcoQuery\QueryService;
use Doctrine\DBAL\DriverManager;
use Rhumsaa\Uuid\Uuid;

class DbalArticleRepo implements ArticleRepo, QueryService
{
    private $conn;
    private $dateTimeFormat = 'Y-m-d H:i:sO';

    public function __construct($databaseUrl)
    {
        $dbopts = parse_url($databaseUrl);
        $connParams = [
            'driver' => 'pdo_pgsql',
            'host' => $dbopts['host'],
            'user' => $dbopts['user'],
            'password' => $dbopts['pass'],
            'dbname' => ltrim($dbopts['path'], '/'),
        ];
        $this->conn = DriverManager::getConnection($connParams);
    }

    public function remove(Article $article)
    {
        $this->conn->delete('aco_article', ['uuid' => $article->getUuid()->toString()]);
    }

    public function find(Uuid $uuid)
    {
        $qb = $this->conn->createQueryBuilder();
        $query = $qb
            ->select('*')
            ->from('aco_article')
            ->where('uuid = ?')
            ->setParameters([$uuid], ['guid'])
            ;
        $st = $query->execute();
        $xs = $st->fetchAll();
        if (count($xs) == 0) {
            throw new ArticleDoesNotExistException();
        }

        $x = $xs[0];
        $articleSource = new ArticleSource(
            new Url($x['article_source_url']),
            $x['article_source_content']
        );
        $createdAt = \DateTime::createFromFormat($this->dateTimeFormat, $x['created_at']);
        $article = new Article($uuid, $x['title'], $createdAt, $articleSource, $x['content']);

        return $article;
    }

    public function store(Article $article)
    {
        $articleSource = $article->getArticleSource();
        $data = [
            'uuid' => $article->getUuid(),
            'title' => $article->getTitle(),
            'created_at' => $article->getCreatedAt(),
            'article_source_url' => $articleSource->getUrl()->getUrl(),
            'article_source_content' => $articleSource->getContent(),
            'content' => $article->getContent(),
            'removed' => $article->isRemoved(),
        ];
        $types = [
            'guid',
            'text',
            'datetimetz',
            'text',
            'text',
            'text',
            'boolean',
        ];

        try {
            $this->find($article->getUuid());
            $this->conn->update('aco_article',
                $data,
                ['uuid' => $article->getUuid()],
                $types);
        } catch (ArticleDoesNotExistException $e) {
            $this->conn->insert('aco_article', $data, $types);
        }
    }

    public function findArticles($offset = 0, $limit = 0)
    {
        $qb = $this->conn->createQueryBuilder();
        $query = $qb
            ->select('uuid', 'title')
            ->from('aco_article')
            ->where('not removed = true')
            ->orderBy('created_at', 'desc')
        ;
        $st = $query->execute();

        return $st->fetchAll(\PDO::FETCH_CLASS, 'AcoQuery\ListArticle');
    }

    public function findArticle($uuid)
    {
        $qb = $this->conn->createQueryBuilder();
        $query = $qb
            ->select('uuid', 'title', 'created_at', 'article_source_url',
                'article_source_content', 'content', 'removed')
            ->from('aco_article')
            ->where('uuid = ?')
            ;
        $query->setParameters([$uuid], ['guid']);
        $st = $query->execute();
        $xs = $st->fetchAll(\PDO::FETCH_CLASS, 'AcoQuery\FullArticle');
        if (count($xs) === 0) {
            throw new ArticleDoesNotExistException();
        }

        $r = $xs[0];
        $r->created_at = \DateTime::createFromFormat($this->dateTimeFormat, $r->created_at);

        return $r;
    }
}
