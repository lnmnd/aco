<?php

namespace Aco\Domain\Aco;

use Symfony\Component\DomCrawler\Crawler;
use DOMDocument;
use DOMElement;

class ContentExtractor
{
    public static function extractContent($content)
    {
        $elements = self::getAllElements($content);
        $biggestNum = 0;
        $biggestTag = null;
        foreach ($elements as $el) {
            $pNum = self::countParagraphs($el);
            if ($pNum > $biggestNum) {
                $biggestNum = $pNum;
                $biggestTag = $el;
            }
        }

        if ($biggestNum == 0) {
            return '';
        }

        return self::innerHtml(
        self::removeStyles($biggestTag)
    );
    }

    private static function getAllElements($content)
    {
        $crawler = new Crawler($content);

        return $crawler->filter('*');
    }

    private static function countParagraphs($node)
    {
        $n = 0;
        foreach ($node->childNodes as $c) {
            if (isset($c->tagName) && (($c->tagName == 'p') || ($c->tagName == 'P'))) {
                ++$n;
            }
        }

        return $n;
    }

    private static function removeStyles(DOMElement $el)
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
                    self::removeStyles($c);
                }
            }
        }

        return $el;
    }

    private static function innerHtml(\DOMElement $element)
    {
        $innerHTML = '';
        $children = $element->childNodes;
        foreach ($children as $child) {
            $tmpDom = new DOMDocument();
            $tmpDom->appendChild($tmpDom->importNode($child, true));
            $innerHTML .= trim($tmpDom->saveHTML());
        }

        return $innerHTML;
    }
}
