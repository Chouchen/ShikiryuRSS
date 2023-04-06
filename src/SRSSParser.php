<?php

namespace Shikiryu\SRSS;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use Shikiryu\SRSS\Entity\Channel;
use Shikiryu\SRSS\Entity\Channel\Image;
use Shikiryu\SRSS\Entity\Item;

class SRSSParser extends DomDocument
{
    private SRSS $doc;
    private DOMXPath $xpath;

    public function __construct()
    {
        libxml_use_internal_errors(true);
        parent::__construct();
        $this->xpath = new DOMXpath($this);
        $this->doc = new SRSS();
    }

    /**
     * Destructor
     * manage of libxml errors
     */
    public function __destruct()
    {
        foreach (libxml_get_errors() as $error) {
            error_log($error->message, 3, 'error.log');
        }
        libxml_clear_errors();
    }

    /**
     * @param string $link
     *
     * @return \Shikiryu\SRSS\SRSS
     * @throws \Shikiryu\SRSS\SRSSException
     */
    public function parse(string $link)
    {
        if(@$this->load($link)) { // We don't want the warning in case of bad XML. Let's manage it with an exception.
            $channel = $this->getElementsByTagName('channel');
            if($channel->length === 1){ // Good URL and good RSS
                $this->_loadAttributes(); // loading channel properties
                $this->getItems(); // loading all items

                return $this->doc;
            }

            throw new SRSSException('invalid file '.$link);
        }

        throw new SRSSException('Can not open file '.$link);
    }

    /**
     * @return Item[]
     * @throws \Shikiryu\SRSS\SRSSException
     */
    private function getItems(): mixed
    {
        $channel = $this->_getChannel();
        /** @var DOMNodeList $items */
        $items = $channel->getElementsByTagName('item');
        $length = $items->length;
        $this->doc->items = [];
        if ($length > 0) {
            for($i = 0; $i < $length; $i++) {
                $this->doc->items[$i] = SRSSItem::read($items->item($i));
            }
        }

        return $this->doc->items;
    }
    /**
     * putting all RSS attributes into the object
     * @throws SRSSException
     */
    private function _loadAttributes(): void
    {
        $node_channel = $this->_getChannel();
        $this->doc->channel = new Channel();

        foreach($node_channel->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->nodeName !== 'item') {
                if($child->nodeName === 'image') {
                    $image = new Image();
                    foreach($child->childNodes as $children) {
                        if($children->nodeType == XML_ELEMENT_NODE) {
                            $image->{$child->nodeName} = $children->nodeValue;
                        }
                    }
                    $this->doc->channel->image = $image;

                } else {
                    $this->doc->channel->{$child->nodeName} = $child->nodeValue;
                }
            }
        }
    }


    /**
     * getter of current RSS channel
     * @return \DOMNode
     * @throws SRSSException
     */
    private function _getChannel(): \DOMNode
    {
        $channel = $this->getElementsByTagName('channel');
        if($channel->length != 1) {
            throw new SRSSException('channel node not created, or too many channel nodes');
        }

        return $channel->item(0);
    }

    /**
     * getter of "image"'s channel attributes
     * @return string|array
     * TODO
     */
    public function image()
    {
        $args = func_get_args();
        if (func_num_args() == 0) {
            $args[0] = 'url';
        }
        $img = $this->xpath->query('//channel/image');
        if ($img->length != 1) { // <image> is not in channel
            return null;
        }
        $img = $img->item(0);
        $r = [];
        foreach ($img->childNodes as $child) {
            if ($child->nodeType == XML_ELEMENT_NODE && in_array($child->nodeName, $args)) {
                $r[$child->nodeName] = $child->nodeValue;
            }
        }

        return (func_num_args() > 1) ? $r : $r[$args[0]];
    }
}