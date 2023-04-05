<?php

namespace Shikiryu\SRSS;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\Entity\Media\Content;

/**
 * @property string|null $description
 */
class SRSSItem extends DomDocument
{

    protected DOMNode $node; // item node
    protected $attr; // item's properties

    // possible properties' names
    protected static $possibilities = [
        'title'         => 'nohtml',
        'link'          => 'link',
        'description'   => 'html',
        'author'        => 'email',
        'category'      => 'nohtml',
        'comments'      => 'link',
        'enclosure'     => '',
        'guid'          => 'nohtml',
        'pubDate'       => 'date',
        'source'        => 'link',
        'media:group'   => 'folder',
        'media:content' => '',
    ];

    /**
     * Constructor
     *
     * @param DomNode $node
     */
    public function __construct($node = null)
    {
        parent::__construct();
//        if ($node instanceof DOMElement) $this->node = $this->importNode($node, true);
//        else $this->node = $this->importNode(new DomElement('item'));
//        $this->_loadAttributes();
    }

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
                if (array_key_exists($child->nodeName, self::$possibilities) && self::$possibilities[$child->nodeName] === 'folder') {
                    self::_loadChildAttributes($item, $child);
                } elseif  ($child->nodeName === 'media:group') {
                    // TODO
                } elseif  ($child->nodeName === 'media:content') {
                    $item->{$child->nodeName} = new Content($child);
                } else {
                    $item->{$child->nodeName} = $child->nodeValue;
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

    /**
     * getter of item DomElement
     */
    public function getItem(): ?\DOMNode
    {
        $this->appendChild($this->node);

        return $this->getElementsByTagName('item')->item(0);
    }

    /**
     * setter for enclosure's properties
     *
     * @param $url    string url
     * @param $length int length
     * @param $type   string type
     * @throws DOMException
     */
    public function setEnclosure(string $url, int $length, string $type): void
    {
        $array = [];
        $url = SRSSTools::checkLink($url);
        $array['url'] = $url;
        $length = SRSSTools::checkInt($length);
        $array['length'] = $length;
        $type = SRSSTools::noHTML($type);
        $array['type'] = $type;
        if ($this->enclosure == null) {
            $node = $this->createElement('enclosure');
            $node->setAttribute('url', $url);
            $node->setAttribute('length', $length);
            $node->setAttribute('type', $type);
            $this->node->appendChild($node);
        }
        $this->attr['enclosure'] = $array;
    }

    /**
     * check if current item is valid (following specifications)
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->description != null;
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
     * main setter for properties
     *
     * @param $name
     * @param $val
     *
     * @throws SRSSException|DOMException
     */
    public function __set($name, $val)
    {
        if (!array_key_exists($name, $this->possibilities)) {
            throw new SRSSException(sprintf('%s is not a possible item (%s)', $name, implode(', ', array_keys($this->possibilities))));
        }

        $flag = $this->possibilities[$name];
        if ($flag !== '')
            $val = SRSSTools::check($val, $flag);
        if (!empty($val)) {
            if ($val instanceof DOMElement) {
                $this->node->appendChild($val);
            } elseif ($this->$name == null) {
                $this->node->appendChild(new DomElement($name, $val));
            }
            $this->attr[$name] = $val;
        }
    }

    /**
     * main getter for properties
     *
     * @param $name
     *
     * @return null|string
     * @throws SRSSException
     */
    public function __get($name)
    {
        if (isset($this->attr[$name]))
            return $this->attr[$name];
        if (array_key_exists($name, $this->possibilities)) {
            $tmp = $this->node->getElementsByTagName($name);
            if ($tmp->length != 1) return null;

            return $tmp->item(0)->nodeValue;
        }

        throw new SRSSException(sprintf('%s is not a possible item (%s)', $name, implode(', ', array_keys($this->possibilities))));
    }

    /**
     * display item's XML
     * see DomDocument's docs
     */
    public function show()
    {
        return $this->saveXml();
    }

    /**
     * transform current item's object into an array
     * @return array
     */
    public function toArray()
    {
        $infos = [];
        foreach ($this->attr as $attrName => $attrVal) {
            $infos[$attrName] = $attrVal;
        }

        return $infos;
    }
}