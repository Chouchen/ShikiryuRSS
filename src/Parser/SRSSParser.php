<?php

namespace Shikiryu\SRSS\Parser;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use Shikiryu\SRSS\Entity\Channel;
use Shikiryu\SRSS\Entity\Channel\Image;
use Shikiryu\SRSS\Exception\ChannelNotFoundInRSSException;
use Shikiryu\SRSS\Exception\SRSSException;
use Shikiryu\SRSS\Exception\UnreadableRSSException;
use Shikiryu\SRSS\SRSS;

class SRSSParser extends DomDocument
{
    private SRSS $doc;

    public function __construct()
    {
        libxml_use_internal_errors(true);
        parent::__construct();
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
     * @return SRSS
     * @throws ChannelNotFoundInRSSException
     * @throws SRSSException
     * @throws UnreadableRSSException
     */
    public function parse(string $link): SRSS
    {
        if (@$this->load($link)) { // We don't want the warning in case of bad XML. Let's manage it with an exception.
            $channel = $this->getElementsByTagName('channel');
            if($channel->length === 1){ // Good URL and good RSS
                $this->_parseChannel(); // loading channel properties
                $this->_parseItems(); // loading all items

                return $this->doc;
            }

            throw new ChannelNotFoundInRSSException($link);
        }

        throw new UnreadableRSSException($link);
    }

    /**
     * @throws SRSSException
     */
    private function _parseItems(): void
    {
        $channel = $this->_getChannel();
        /** @var DOMNodeList $items */
        $items = $channel->getElementsByTagName('item');
        $length = $items->length;
        $this->doc->items = [];
        if ($length > 0) {
            for($i = 0; $i < $length; $i++) {
                $this->doc->items[$i] = ItemParser::read($items->item($i));
            }
        }
    }

    /**
     * putting all RSS attributes into the object
     * @throws SRSSException
     */
    private function _parseChannel(): void
    {
        $node_channel = $this->_getChannel();
        $this->doc->channel = new Channel();

        foreach($node_channel->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->nodeName !== 'item') {
                if($child->nodeName === 'image') {
                    $image = new Image();
                    foreach($child->childNodes as $children) {
                        if($children->nodeType === XML_ELEMENT_NODE) {
                            $image->{$children->nodeName} = $children->nodeValue;
                        }
                    }
                    $this->doc->channel->image = $image;

                } elseif($child->nodeName === 'cloud') {
                    $cloud = new Channel\Cloud();
                    foreach($child->attributes as $attribute) {
                        $cloud->{$attribute->name} = $attribute->value;
                    }
                    $this->doc->channel->cloud = $cloud;
                }  elseif($child->nodeName === 'category') {
                    $category = new Channel\Category();
                    foreach($child->attributes as $attribute) {
                        $category->{$attribute->name} = $attribute->value;
                    }
                    $category->value = $child->nodeValue;
                    $this->doc->channel->category = $category;
                } else {
                    $this->doc->channel->{$child->nodeName} = $child->nodeValue;
                }
            }
        }
    }


    /**
     * getter of current RSS channel
     * @return DOMNode
     * @throws SRSSException
     */
    private function _getChannel(): DOMNode
    {
        $channel = $this->getElementsByTagName('channel');
        if($channel->length !== 1) {
            throw new ChannelNotFoundInRSSException('channel node not created, or too many channel nodes');
        }

        return $channel->item(0);
    }
}