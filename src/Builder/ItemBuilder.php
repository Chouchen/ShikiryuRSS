<?php

namespace Shikiryu\SRSS\Builder;

use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\Entity\Media\Content;

class ItemBuilder
{
    private \DOMDocument $document;

    /**
     * @param \DOMDocument $document
     */
    public function __construct(\DOMDocument $document)
    {
        $this->document = $document;
    }

    /**
     * @param \Shikiryu\SRSS\Entity\Item $item
     *
     * @return \DOMElement|false
     * @throws \DOMException
     */
    public function build(Item $item): bool|\DOMElement
    {
        $node = $this->document->createElement('item');

        foreach (array_filter($item->toArray()) as $name => $value) {
            if ($name === 'category') {
                /** @var \Shikiryu\SRSS\Entity\Item\Category $category */
                foreach ($value as $category) {
                    $node->appendChild($this->buildCategory($category));
                }
            } elseif ($name === 'medias') {
                $group = null;
                if (count($value) > 1) {
                    $group = $node->appendChild($this->document->createElement('media:group'));
                }
                foreach ($value as $media) {
                    if (null === $group) {
                        $node->appendChild($this->buildMedia($media));
                    } else {
                        $group->appendChild($this->buildMedia($media));
                    }
                }
                if ($group !== null) {
                    $node->appendChild($group);
                }
            } elseif ($name === 'enclosure') {
                $node->appendChild($this->buildEnclosure($value));
            } elseif ($name === 'source') {
                $node->appendChild($this->buildSource($value));
            } else {
                $new_node = $this->document->createElement($name, $value);
                $node->appendChild($new_node);
            }
        }

        return $node;
    }

    private function buildCategory(Item\Category $category)
    {
        $node = $this->document->createElement('category', $category->value);
        $node->setAttribute('domain', $category->domain);

        return $node;
    }

    private function buildEnclosure(Item\Enclosure $enclosure)
    {
        $node = $this->document->createElement('enclosure');
        foreach (get_object_vars($enclosure) as $name => $value) {
            if (!is_array($value)) {
                $node->setAttribute($name, $value);
            }
        }

        return $node;
    }

    private function buildSource(Item\Source $source)
    {
        $node = $this->document->createElement('source', $source->value);
        $node->setAttribute('url', $source->url);

        return $node;
    }

    private function buildMedia(Content $media)
    {
        $node = $this->document->createElement('media:content');
        foreach (get_object_vars($media) as $name => $value) {
            if (!is_array($value)) {
                $node->setAttribute($name, $value);
            }
        }

        return $node;
    }


}