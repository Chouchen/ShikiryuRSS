<?php

namespace Shikiryu\SRSS\Parser;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\Exception\SRSSException;
use Shikiryu\SRSS\SRSSTools;

/**
 * @property string|null $description
 */
class ItemParser extends DomDocument
{

    protected DOMNode $node; // item node
    protected $attr; // item's properties

    /**
     * Constructor
     *
     * @param DomNode $node
     */
    public function __construct($node = null)
    {
        parent::__construct();
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

    /**
     * getter of item DomElement
     */
    public function getItem(): ?DOMNode
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