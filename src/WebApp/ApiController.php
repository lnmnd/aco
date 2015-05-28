<?php

namespace WebApp;

use AcoQuery\QueryService;
use AcoQuery\Exception\ArticleCollectionNotFoundException;
use Aco\CommandBus;
use Aco\Command\AddArticleCollectionCommand;
use Aco\Exception\BadUrlException;
use Aco\Exception\NoArticlesException;
use Aco\Exception\CannotFetchUrlException;
use Aco\Exception\CannotExtractContentException;

class ApiController
{
	private $commandBus;
	private $queryService;
	
	public function __construct(CommandBus $commandBus, QueryService $queryService)
	{
		$this->commandBus = $commandBus;
		$this->queryService = $queryService;
	}
	
	public function optionArticleCollection()
	{
		header('Access-Control-Allow-Methods: OPTIONS, POST, GET');
		header('Access-Control-Allow-Headers: content-type');
		return $this->respond(new \stdClass());
	}
	
	public function postArticleCollection()
	{
		try {
			$contents = file_get_contents('php://input');
			$input = json_decode($contents);
			if ($this->badAcoInput($input)) {
				header('HTTP/1.0 400 Bad Request');
				return $this->respond(new \stdClass());
			}
	
			$res = new \stdClass();
			$res->uuid = $this->commandBus->handle(
					new AddArticleCollectionCommand($input->title, $input->description, $input->urls)
			);
			return $this->respond($res);
		} catch (NoArticlesException $e) {
			header('HTTP/1.0 400 Bad Request');
			return $this->respond(['error' => 'No articles']);
		} catch (BadUrlException $e) {
			header('HTTP/1.0 400 Bad Request');
			return $this->respond(['error' => 'Bad url']);
		} catch (CannotFetchUrlException $e) {
			header('HTTP/1.0 500  Internal Server Error');
			return $this->respond(['error' => 'Cannot fetch url']);
		} catch (CannotExtractContentException $e) {
			header('HTTP/1.0 500  Internal Server Error');
			return $this->respond(['error' => 'Cannot extract content']);
		}
	}
	
	public function getArticleCollections()
	{
		return $this->respond($this->queryService->getArticleCollections());
	}
	
	public function getArticleCollection($uuid)
	{
		try {
			return $this->respond($this->queryService->getArticleCollection($uuid));
		} catch (ArticleCollectionNotFoundException $e) {
			header('HTTP/1.0. 404 Not Found');
			return $this->respond(new \stdClass());
		}
	}
	
	private function badAcoInput($input)
	{
		return !isset($input->title)
		|| !isset($input->description)
		|| !isset($input->urls)
		;
	}
	
	private function respond($data)
	{
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: application/json');
		echo json_encode($data);
	}
}