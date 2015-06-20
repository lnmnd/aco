<?php

namespace Aco\Handler;

use Rhumsaa\Uuid\Uuid;
use Aco\Handler;
use Aco\Command\RemoveArticleCollectionCommand;
use Aco\ArticleCollectionRepository;
use Aco\Exception\DoesNotExistException;

class RemoveArticleCollectionHandler implements Handler
{
    /**
     * @var ArticleCollectionRepository
     */
    private $articleCollectionRepository;

    public function __construct(ArticleCollectionRepository $articleCollectionRepository)
    {
        $this->articleCollectionRepository = $articleCollectionRepository;
    }

    /**
     * @see \Aco\Handler::handle()
     * @param  RemoveArticleCollectionCommand $command
     * @throws DoesNotExistException
     */
    public function handle($command)
    {
        $uuid = Uuid::fromString($command->uuid);
        $articleCollection = $this->articleCollectionRepository->get($uuid);
        $this->articleCollectionRepository->remove($articleCollection);
    }
}
