<?php

namespace Aco;

use Symfony\Component\DomCrawler\Crawler;
use DOMDocument;
use DOMElement;
use Aco\Exception\CannotExtractContentException;

function extractTitle($content)
{
    $crawler = new Crawler($content);
    $nodes = $crawler->filter('head > title');

    if (count($nodes) === 0) {
        return 'unamed';
    }

    return $nodes->first()->text();
}

function extractContent($content)
{
    $elements = getAllElements($content);
    $biggestNum = 0;
    $biggestTag = null;
    foreach ($elements as $el) {
        $pNum = countParagraphs($el);
        if ($pNum > $biggestNum) {
            $biggestNum = $pNum;
            $biggestTag = $el;
        }
    }

    if ($biggestNum == 0) {
        throw new CannotExtractContentException();
    }

    return innerHtml(
        removeStyles($biggestTag)
    );
}

function getAllElements($content)
{
    $crawler = new Crawler($content);

    return $crawler->filter('*');
}

function countParagraphs($node)
{
    $n = 0;
    foreach ($node->childNodes as $c) {
        if (isset($c->tagName) && (($c->tagName == 'p') || ($c->tagName == 'P'))) {
            $n++;
        }
    }

    return $n;
}

function removeStyles(DOMElement $el)
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
                removeStyles($c);
            }
        }
    }

    return $el;
}

function innerHtml(\DOMElement $element)
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
