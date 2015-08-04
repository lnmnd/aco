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
    private $title;
    private $content;

    public function __construct(Url $url, $originalContent)
    {
        $this->url = $url;
        $this->originalContent = $originalContent;
        $this->title = $this->extractTitle($this->originalContent);
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

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    private function extractTitle($content)
    {
        $crawler = new Crawler($content);
        $nodes = $crawler->filter('head > title');

        if (count($nodes) === 0) {
            return 'unamed';
        }

        return $nodes->first()->text();
    }

    private function extractContent($content)
    {
        $elements = $this->getAllElements($content);
        $biggestNum = 0;
        $biggestTag = null;
        foreach ($elements as $el) {
            $pNum = $this->countParagraphs($el);
            if ($pNum > $biggestNum) {
                $biggestNum = $pNum;
                $biggestTag = $el;
            }
        }

        if ($biggestNum == 0) {
            throw new CannotExtractContentException();
        }

        return $this->innerHtml(
            $this->removeStyles($biggestTag)
        );
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
            $tmpDom = new DOMDocument();
            $tmpDom->appendChild($tmpDom->importNode($child, true));
            $innerHTML.=trim($tmpDom->saveHTML());
        }

        return $innerHTML;
    }
}
