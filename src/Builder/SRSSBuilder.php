<?php

namespace Shikiryu\SRSS\Builder;

use DOMDocument;
use DOMElement;
use Shikiryu\SRSS\Entity\Channel;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\Parser\ItemParser;
use Shikiryu\SRSS\SRSS;
use Shikiryu\SRSS\SRSSTools;

class SRSSBuilder extends DomDocument
{
    public function build(SRSS $srss, string $filepath)
    {
        $root = $this->createElement('rss');
        $root->setAttribute('version', '2.0');
        $channel = $this->createElement('channel');

        $this->appendChannelToDom($srss->channel, $channel);

        $this->appendItemsToDom($srss->items, $channel);

        $root->appendChild($channel);
        $this->appendChild($root);
        $this->encoding = 'UTF-8';
        $this->generator = 'Shikiryu RSS';
        $this->formatOutput = true;
        $this->preserveWhiteSpace = false;
        // $docs = 'http://www.scriptol.fr/rss/RSS-2.0.html';

        $this->save($filepath);
    }

    /**
     * add a SRSS Item as an item into current RSS as first item
     *
     * @param ItemParser $item
     */
    public function addItemBefore(ItemParser $item)
    {
        $node = $this->importNode($item->getItem(), true);
        $items =  $this->getElementsByTagName('item');
        if($items->length != 0){
            $firstNode = $items->item(0);
            if($firstNode != null)
                $firstNode->parentNode->insertBefore($node, $firstNode);
            else
                $this->addItem($item);
        }
        else
            $this->addItem($item);
    }

    /**
     * add a SRSS Item as an item into current RSS
     *
     * @param ItemParser $item
     */
    public function addItem(ItemParser $item)
    {
        $node = $this->importNode($item->getItem(), true);
        $channel = $this->_getChannel();
        $channel->appendChild($node);
    }

    /**
     * display XML
     * see DomDocument's docs
     */
    public function show()
    {
        // TODO build
        return $this->saveXml();
    }

    /**
     * setter of "image"'s channel attributes
     * @param $url string  picture's url
     * @param $title string picture's title
     * @param $link string link on the picture
     * @param $width int width
     * @param $height int height
     * @param $description string description
     * TODO
     */
    public function setImage($url, $title, $link, $width = 0, $height = 0, $description = '')
    {
        $channel = $this->_getChannel();
        $array = [];
        $url = SRSSTools::checkLink($url);
        $array['url'] = $url;
        $title = SRSSTools::noHTML($title);
        $array['title'] = $title;
        $link = SRSSTools::checkLink($link);
        $array['link'] = $link;
        if($width != 0)
        {
            $width = SRSSTools::checkInt($width);
            $array['width'] = $width;
        }
        if($height != 0)
        {
            $height = SRSSTools::checkInt($height);
            $array['height'] = $height;
        }
        if($description != 0)
        {
            $description = SRSSTools::noHTML($description);
            $array['description'] = $description;
        }
        if($this->image == null)
        {
            $node = $this->createElement('image');
            $urlNode = $this->createElement('url', $url);
            $titleNode = $this->createElement('title', $title);
            $linkNode = $this->createElement('link', $link);
            $node->appendChild($urlNode);
            $node->appendChild($titleNode);
            $node->appendChild($linkNode);
            if($width != 0)
            {
                $widthNode = $this->createElement('width', $width);
                $node->appendChild($widthNode);
            }
            if($height != 0)
            {
                $heightNode = $this->createElement('height', $height);
                $node->appendChild($heightNode);
            }
            if($description != '')
            {
                $descNode = $this->createElement('description', $description);
                $node->appendChild($descNode);
            }
            $channel->appendChild($node);
        }
        $this->attr['image'] = $array;
    }

    /**
     * setter of "cloud"'s channel attributes
     * @param $domain string domain
     * @param $port int port
     * @param $path string path
     * @param $registerProcedure string register procedure
     * @param $protocol string protocol
     * TODO
     */
    public function setCloud($domain, $port, $path, $registerProcedure, $protocol)
    {
        $channel = $this->_getChannel();
        $array = array();
        $domain = SRSSTools::noHTML($domain);
        $array['domain'] = $domain;
        $port = SRSSTools::checkInt($port);
        $array['port'] = $port;
        $path = SRSSTools::noHTML($path);
        $array['path'] = $path;
        $registerProcedure = SRSSTools::noHTML($registerProcedure);
        $array['registerProcedure'] = $registerProcedure;
        $protocol = SRSSTools::noHTML($protocol);
        $array['protocol'] = $protocol;
        if($this->cloud == null)
        {
            $node = $this->createElement('cloud');
            $node->setAttribute('domain', $domain);
            $node->setAttribute('port', $port);
            $node->setAttribute('path', $path);
            $node->setAttribute('registerProcedure', $registerProcedure);
            $node->setAttribute('protocol', $protocol);
            $channel->appendChild($node);
        }
        $this->attr['cloud'] = $array;
    }

    private function appendChannelToDom(Channel $channel, DOMElement $node)
    {
        foreach (array_filter($channel->toArray(), fn($el) => !empty($el)) as $name => $value) {
            $new_node = $this->createElement($name, $value);
            $node->appendChild($new_node);
        }
    }

    private function appendItemsToDom(array $items, DOMElement $channel)
    {
        foreach ($items as $item) {
            $this->appendItemToDom($item, $channel);
        }
    }

    private function appendItemToDom(Item $item, DOMElement $channel)
    {
        $itemNode = $this->createElement('item');
        foreach (array_filter($item->toArray()) as $name => $value) {
            $new_node = $this->createElement($name, $value);
            $itemNode->appendChild($new_node);
        }
        $channel->appendChild($itemNode);
    }
}