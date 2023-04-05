<?php

namespace Shikiryu\SRSS;

use DOMDocument;
use DOMXPath;
use Iterator;

class SRSS extends DomDocument implements Iterator
{
    protected $xpath; // xpath engine
    protected $items; // array of SRSSItems
    protected $attr; // array of RSS attributes
    private $position; // Iterator position

    // lists of possible attributes for RSS
    protected $possibleAttr = [
        'title'				=> 'nohtml',
        'link'				=> 'link',
        'description'		=> 'html',
        'language'			=> '',
        //'language'			=> 'lang',
        'copyright'			=> 'nohtml',
        'pubDate'			=> 'date',
        'lastBuildDate'		=> 'date',
        'category'			=> 'nohtml',
        'docs'				=> 'link',
        'cloud'				=> '',
        'generator'			=> 'nohtml',
        'managingEditor'	=> 'email',
        'webMaster'			=> 'email',
        'ttl'				=> 'int',
        'image'				=> '',
        'rating'			=> 'nohtml',
        //'textInput'			=> '',
        'skipHours'			=> 'hour',
        'skipDays'			=> 'day',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        libxml_use_internal_errors(true);
        parent::__construct();
        $this->xpath = new DOMXpath($this);
        $this->attr = array();
        $this->items = array();
        $this->position = 0;
        $this->formatOutput = true;
        $this->preserveWhiteSpace = false;
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
     * @param $link string url of the rss
     * @throws SRSSException
     * @return SRSS
     */
    public static function read($link): SRSS
    {
        $doc = new SRSS;
        if(@$doc->load($link)) // We don't want the warning in case of bad XML. Let's manage it with an exception.
        {
            $channel = $doc->getElementsByTagName('channel');
            if($channel->length == 1){ // Good URL and good RSS
                $doc->_loadAttributes(); // loading channel properties
                $doc->getItems(); // loading all items
                return $doc;
            }

            throw new SRSSException('invalid file '.$link);
        }

        throw new SRSSException('Can not open file '.$link);
    }

    /**
     * @return SRSS
     */
    public static function create()
    {
        $doc = new SRSS;
        $root = $doc->createElement('rss');
        $root->setAttribute('version', '2.0');
        $channel = $doc->createElement('channel');
        $root->appendChild($channel);
        $doc->appendChild($root);
        $doc->encoding = "UTF-8";
        $doc->generator = 'Shikiryu RSS';
        // $docs = 'http://www.scriptol.fr/rss/RSS-2.0.html';
        return $doc;
    }

    /**
     * getter of "image"'s channel attributes
     * @return string|array
     */
    public function image()
    {
        $args = func_get_args();
        if(func_num_args() == 0) $args[0] = 'url';
        $img = $this->xpath->query('//channel/image');
        if($img->length != 1) return null; // <image> is not in channel
        $img = $img->item(0);
        $r = array();
        foreach($img->childNodes as $child)
        {
            if($child->nodeType == XML_ELEMENT_NODE && in_array($child->nodeName, $args))
            {
                $r[$child->nodeName] = $child->nodeValue;
            }
        }
        return (func_num_args() > 1) ? $r : $r[$args[0]];
    }

    /**
     * setter of "image"'s channel attributes
     * @param $url string  picture's url
     * @param $title string picture's title
     * @param $link string link on the picture
     * @param $width int width
     * @param $height int height
     * @param $description string description
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

    /**
     * check if current RSS is a valid one (based on specifications)
     * @return bool
     */
    public function isValid()
    {
        $valid = true;
        $items = $this->getItems();
        $invalidItems = array();
        $i = 1;
        foreach($items as $item){
            if($item->isValid() === false){
                $invalidItems[] = $i;
                $valid = false;
            }
            $i++;
        }
        return ($valid && $this->title != null && $this->link != null && $this->description != null);
    }

    /**
     * getter of current RSS channel
     * @return DOMElement
     * @throws SRSSException
     */
    private function _getChannel()
    {
        $channel = $this->getElementsByTagName('channel');
        if($channel->length != 1) throw new SRSSException('channel node not created, or too many channel nodes');
        return $channel->item(0);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->attr[$name]);
    }

    /**
     * setter of others attributes
     * @param $name
     * @param $val
     * @throws SRSSException
     */
    public function __set($name, $val)
    {
        $channel = $this->_getChannel();
        if(array_key_exists($name, $this->possibleAttr)){
            $flag = $this->possibleAttr[$name];
            $val = SRSSTools::check($val, $flag);
            if(!empty($val)){
                if($this->$name == null){
                    $node = $this->createElement($name, $val);
                    $channel->appendChild($node);
                }
                $this->attr[$name] = $val;
            }
        }else{
            throw new SRSSException($name.' is not a possible item');
        }
    }

    /**
     * getter of others attributes
     * @param $name
     * @return null|string
     * @throws SRSSException
     */
    public function __get($name)
    {
        if(isset($this->attr[$name]))
            return $this->attr[$name];
//		$channel = $this->_getChannel();
        if(array_key_exists($name, $this->possibleAttr)){
            $tmp = $this->xpath->query('//channel/'.$name);
            if($tmp->length != 1) return null;
            return $tmp->item(0)->nodeValue;
        }else{
            throw new SRSSException($name.' is not a possible value.');
        }
    }

    /**
     * add a SRSS Item as an item into current RSS as first item
     * @param SRSSItem $item
     */
    public function addItemBefore(SRSSItem $item)
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
     * @param SRSSItem $item
     */
    public function addItem(SRSSItem $item)
    {
        $node = $this->importNode($item->getItem(), true);
        $channel = $this->_getChannel();
        $channel->appendChild($node);
    }

    /**
     * rewind from Iterator
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * current from Iterator
     */
    public function current() {
        return $this->items[$this->position];
    }

    /**
     * key from Iterator
     */
    public  function key() {
        return $this->position;
    }

    /**
     * next from Iterator
     */
    public  function next() {
        ++$this->position;
    }

    /**
     * valid from Iterator
     */
    public  function valid() {
        return isset($this->items[$this->position]);
    }

    /**
     * getter of 1st item
     * @return SRSSItem
     */
    public function getFirst()
    {
        return $this->getItem(1);
    }

    /**
     * getter of last item
     * @return SRSSItem
     */
    public function getLast()
    {
        $items = $this->getItems();
        return $items[count($items)-1];
    }

    /**
     * getter of an item
     * @param $i int
     * @return SRSSItem
     */
    public function getItem($i)
    {
        $i--;
        return isset($this->items[$i]) ? $this->items[$i] : null;
    }

    /**
     * getter of all items
     * @return SRSSItem[]
     * @throws SRSSException
     */
    public function getItems()
    {
        $channel = $this->_getChannel();
        $item = $channel->getElementsByTagName('item');
        $length = $item->length;
        $this->items = [];
        if($length > 0){
            for($i = 0; $i < $length; $i++)
            {
                $this->items[$i] = new SRSSItem($item->item($i));
            }
        }
        return $this->items;
    }

    /**
     * display XML
     * see DomDocument's docs
     */
    public function show()
    {
        return $this->saveXml();
    }


    /**
     * putting all RSS attributes into the object
     */
    private function _loadAttributes()
    {
        $channel = $this->_getChannel();
        foreach($channel->childNodes as $child)
        {
            if($child->nodeType == XML_ELEMENT_NODE && $child->nodeName != 'item')
            {
                if($child->nodeName == 'image'){
                    foreach($child->childNodes as $children)
                    {
                        if($children->nodeType == XML_ELEMENT_NODE)
                            $this->attr['image'][$children->nodeName] = $children->nodeValue;
                    }
                }
                else
                    $this->attr[$child->nodeName] = $child->nodeValue;
            }
        }
    }

    /**
     * transform current object into an array
     * @return array
     */
    public function toArray()
    {
        $doc = array();
        foreach($this->attr as $attrName => $attrVal)
        {
            $doc[$attrName] = $attrVal;
        }
        foreach($this->getItems() as $item)
        {
            $doc['items'][] = $item->toArray();
        }
        return $doc;
    }
}