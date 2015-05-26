<?php

namespace WebApp;

use AcoQuery\QueryService;
use AcoQuery\Exception\ArticleCollectionNotFoundException;
use Aco\CommandBus;
use Aco\Command\AddArticleCollectionCommand;
use Aco\Exception\BadUrlException;
use Aco\Exception\NoArticlesException;
use Aco\Exception\CannotFetchUrlException;

class ApiController
{
	private $commandBus;
	private $queryService;
	
	public function __construct(CommandBus $commandBus, QueryService $queryService)
	{
		$this->commandBus = $commandBus;
		$this->queryService = $queryService;
	}
	
	public function postArticleCollection()
	{
		try {
			$contents = file_get_contents('php://input');
			$input = json_decode($contents);
			if ($this->badAcoInput($input)) {
				header('HTTP/1.0 400 Bad Request');
				return new \stdClass();
			}
	
			$res = new \stdClass();
			$res->uuid = $this->commandBus->handle(
					new AddArticleCollectionCommand($input->title, $input->description, $input->urls)
			);
			return $res;
		} catch (NoArticlesException $e) {
			header('HTTP/1.0 400 Bad Request');
			return ['error' => 'No articles'];
		} catch (BadUrlException $e) {
			header('HTTP/1.0 400 Bad Request');
			return ['error' => 'Bad url'];
		} catch (CannotFetchUrlException $e) {
			header('HTTP/1.0 500  Internal Server Error');
			return ['error' => 'Cannot fetch url'];
		} catch (CannotExtractContentException $e) {
			header('HTTP/1.0 500  Internal Server Error');
			return ['error' => 'Cannot extract content'];
		}
	}
	
	public function getArticleCollections()
	{
		return $this->queryService->getArticleCollections();
	}
	
	public function getArticleCollection($uuid)
	{
		try {
			return $this->queryService->getArticleCollection($uuid);
		} catch (ArticleCollectionNotFoundException $e) {
			header('HTTP/1.0. 404 Not Found');
			return new \stdClass();
		}
	}
	
	private function badAcoInput($input)
	{
		return !isset($input->title)
		|| !isset($input->description)
		|| !isset($input->urls)
		;
	}
}