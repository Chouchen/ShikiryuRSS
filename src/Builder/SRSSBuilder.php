<?php

namespace Shikiryu\SRSS\Builder;

use DOMDocument;
use DOMElement;
use Shikiryu\SRSS\Entity\Channel;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\Exception\DOMBuilderException;
use Shikiryu\SRSS\SRSS;

class SRSSBuilder extends DomDocument
{
    /**
     * @throws \Shikiryu\SRSS\Exception\DOMBuilderException
     */
    private function buildRSS(SRSS $srss): SRSSBuilder
    {
        try {
            $root = $this->createElement('rss');

            $root->setAttribute('version', '2.0');
            $channel = $this->createElement('channel');

            $srss->channel->generator = 'Shikiryu RSS';

            $this->appendChannelToDom($srss->channel, $channel);

            $this->appendItemsToDom($srss->items, $channel);

            $root->appendChild($channel);
            $this->appendChild($root);
            $this->encoding = 'UTF-8';
            $this->formatOutput = true;
            $this->preserveWhiteSpace = false;
            // $docs = 'http://www.scriptol.fr/rss/RSS-2.0.html';

        } catch (\DOMException $e) {
            throw new DOMBuilderException($e);
        }

        return $this;
    }

    /**
     * @throws \Shikiryu\SRSS\Exception\DOMBuilderException
     */
    public function build(SRSS $srss, string $filepath): void
    {
        $this
            ->buildRSS($srss)
            ->save($filepath);
    }

    /**
     * @return false|string
     * @throws \Shikiryu\SRSS\Exception\DOMBuilderException
     */
    public function show(SRSS $srss): bool|string
    {
        return $this
            ->buildRSS($srss)
            ->saveXml();
    }

    private function appendChannelToDom(Channel $channel, DOMElement $node): void
    {
        foreach (array_filter($channel->toArray(), static fn($el) => !empty($el)) as $name => $value) {
            $new_node = $this->createElement($name, $value);
            $node->appendChild($new_node);
        }
    }

    private function appendItemsToDom(array $items, DOMElement $channel): void
    {
        foreach ($items as $item) {
            $this->appendItemToDom($item, $channel);
        }
    }

    private function appendItemToDom(Item $item, DOMElement $channel): void
    {
        $itemNode = $this->createElement('item');
        foreach (array_filter($item->toArray()) as $name => $value) {
            $new_node = $this->createElement($name, $value);
            $itemNode->appendChild($new_node);
        }
        $channel->appendChild($itemNode);
    }
}