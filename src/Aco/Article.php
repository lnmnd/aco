<?php

namespace Aco;

use Symfony\Component\DomCrawler\Crawler;
use DOMDocument;
use DOMElement;
use Symfony\Component\DomCrawler\Symfony\Component\DomCrawler;
use Aco\Exception\CannotExtractContentException;

class Article
{
	private $url;
	private $originalContent;
	private $content;
	
	public function __construct(UrlFetcher $urlFetcher, Url $url)
	{
		$this->url = $url;
		$this->originalContent = $urlFetcher->fetch($url);
		$this->content = $this->extractContent($this->originalContent);
	}
	
	public function getUrl()
	{
		return $this->url;
	}
	
	public function getOriginalContent()
	{
		return $this->originalContent;
	}
	
	public function getContent()
	{
		return $this->content;
	}
	
	private function extractContent($content)
	{
		$elements = $this->getAllElements($content);
		$biggest_num = 0;
    	$biggest_tag = null;
    	foreach ($elements as $el) {
            $p_num = $this->countParagraphs($el);
            if ($p_num > $biggest_num) {
        		$biggest_num = $p_num;
        		$biggest_tag = $el;
            }
    	}

    	if ($biggest_num == 0) {
    		throw new CannotExtractContentException();
    	}

		return $this->innerHtml(
				$this->removeStyles($biggest_tag));
	}
	
	private function getAllElements($content)
	{
		$crawler = new Crawler($content);
		return $crawler->filter('*');
	}
	
	private function countParagraphs($node)
	{
		$n = 0;
		foreach ($node->childNodes as $c) {
			if (isset($c->tagName) && (($c->tagName == 'p') || ($c->tagName == 'P'))) {
				$n++;
			}
		}
		return $n;
	}
	
	private function removeStyles(DOMElement $el)
	{
		if ($el->nodeName !== 'img') {
			$attrs = [];
			$as = $el->attributes;
			foreach ($as as $x) {
				$attrs[] = $x->name;
			}
			foreach ($attrs as $x) {
				$el->removeAttribute($x);
			}
		}
	
		if ($el->hasChildNodes()) {
			foreach ($el->childNodes as $c) {
				if (get_class($c) == 'DOMElement') {
					$this->removeStyles($c);
				}
			}
		}
	
		return $el;
	}
	
	private function innerHtml(\DOMElement $element)
	{
		$innerHTML = "";
		$children = $element->childNodes;
		foreach ($children as $child) {
			$tmp_dom = new DOMDocument();
			$tmp_dom->appendChild($tmp_dom->importNode($child, true));
			$innerHTML.=trim($tmp_dom->saveHTML());
		}
		
		return $innerHTML;
	}
}