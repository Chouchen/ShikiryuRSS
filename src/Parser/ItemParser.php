<?php

namespace Shikiryu\SRSS\Parser;

use DOMDocument;
use DOMNode;
use Shikiryu\SRSS\Entity\Item;

/**
 * @property string|null $description
 */
class ItemParser extends DomDocument
{

    protected DOMNode $node; // item node

    /**
     * @param Item $item
     * @param $nodes
     *
     * @return void
     */
    private static function _loadChildAttributes(Item $item, $nodes): void
    {
        foreach ($nodes->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->nodeName !== 'item') {
                if ($child->nodeName === 'media:group') {
                    self::_loadChildAttributes($item, $child);
                } elseif  ($child->nodeName === 'media:content') {
                    $item->medias[] = MediaContentParser::read($child);
                }  elseif  ($child->nodeName === 'category') {
                    $category = new Item\Category();
                    foreach($child->attributes as $attribute) {
                        $category->{$attribute->name} = $attribute->value;
                    }
                    $category->value = $child->nodeValue;
                    $item->category[] = $category;
                }  elseif  ($child->nodeName === 'enclosure') {
                    $enclosure = new Item\Enclosure();
                    foreach($child->attributes as $attribute) {
                        $enclosure->{$attribute->name} = $attribute->value;
                    }
                    $item->enclosure = $enclosure;
                }  elseif  ($child->nodeName === 'source') {
                    $source = new Item\Source();
                    foreach($child->attributes as $attribute) {
                        $source->{$attribute->name} = $attribute->value;
                    }
                    $source->value = $child->nodeValue;
                    $item->source = $source;
                } else {
                    $item->{$child->nodeName} = trim($child->nodeValue);
                }
            }
        }
    }

    /**
     * @param DOMNode|null $node
     *
     * @return Item
     */
    public static function read(?DOMNode $node = null): Item
    {
        $item = new Item();
        if ($node instanceof DOMNode) {
            self::_loadChildAttributes($item, $node);
        }

        return $item;
    }
}