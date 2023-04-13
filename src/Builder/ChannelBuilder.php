<?php

namespace Shikiryu\SRSS\Builder;

use DOMElement;
use Shikiryu\SRSS\Entity\Channel;

class ChannelBuilder
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
     * @param \Shikiryu\SRSS\Entity\Channel $channel
     *
     * @return \DOMElement|false
     * @throws \DOMException
     */
    public function build(Channel $channel)
    {
        $node = $this->document->createElement('channel');
        foreach (array_filter($channel->toArray(), static fn($el) => !empty($el)) as $name => $value) {
            if ($name === 'category') {
                /** @var \Shikiryu\SRSS\Entity\Channel\Category $category */
                foreach ($value as $category) {
                    $node->appendChild($this->buildCategory($category));
                }
            } elseif ($name === 'cloud') {
                $node->appendChild($this->buildCloud($value));
            } elseif ($name === 'image') {
                $node->appendChild($this->buildImage($value));
            } else {
                $new_node = $this->document->createElement($name, $value);
                $node->appendChild($new_node);
            }
        }

        return $node;
    }

    /**
     * @param \Shikiryu\SRSS\Entity\Channel\Category $category
     *
     * @return bool|\DOMElement
     * @throws \DOMException
     */
    private function buildCategory(Channel\Category $category): bool|DOMElement
    {
        $node = $this->document->createElement('category', $category->value);
        $node->setAttribute('domain', $category->domain);

        return $node;
    }

    /**
     * @param \Shikiryu\SRSS\Entity\Channel\Cloud $cloud
     *
     * @return \DOMElement|false
     * @throws \DOMException
     */
    private function buildCloud(Channel\Cloud $cloud)
    {
        $node = $this->document->createElement('cloud');
        foreach (get_object_vars($cloud) as $name => $value) {
            if (!is_array($value)) {
                $node->setAttribute($name, $value);
            }
        }

        return $node;
    }

    /**
     * @param \Shikiryu\SRSS\Entity\Channel\Image $image
     *
     * @return \DOMElement|false
     * @throws \DOMException
     */
    private function buildImage(Channel\Image $image)
    {
        $node = $this->document->createElement('image');
        foreach (get_object_vars($image) as $name => $value) {
            if (!is_array($value)) {
                $node->appendChild($this->document->createElement($name, $value));
            }
        }

        return $node;
    }
}