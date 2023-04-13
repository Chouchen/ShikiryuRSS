<?php

namespace Shikiryu\SRSS\Builder;

use DOMDocument;
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

            $srss->channel->generator = 'Shikiryu RSS';

            $channel_builder = new ChannelBuilder($this);
            $channel = $channel_builder->build($srss->channel);

            $item_builder = new ItemBuilder($this);
            foreach ($srss->items as $item) {
                $channel->appendChild($item_builder->build($item));
            }

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
}