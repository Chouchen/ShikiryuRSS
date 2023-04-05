<?php

namespace Shikiryu\SRSS\Entity\Media;

use DOMDocument;
use DOMElement;
use DOMNode;
use Shikiryu\SRSS\SRSSException;
use Shikiryu\SRSS\SRSSTools;

class Content extends DomDocument
{
    protected array $possibilities = [
        'url'          => 'link',
        'fileSize'     => 'int', // TODO
        'type'         => 'media_type', // TODO
        'medium'       => 'media_medium', // TODO
        'isDefault'    => 'bool', // TODO
        'expression'   => 'medium_expression', // TODO
        'bitrate'      => 'int',
        'framerate'    => 'int',
        'samplingrate' => 'float',
        'channels'     => 'int',
        'duration'     => 'int',
        'height'       => 'int',
        'width'        => 'int',
        'lang'         => '',
    ];
    private array $attr = [];

    private DOMNode $node;

    /**
     * Constructor
     *
     * @param DomNode $node
     */
    public function __construct(?\DOMNode $node = null)
    {
        parent::__construct();
        if ($node instanceof DOMElement) {
            $this->node = $this->importNode($node, true);
        } else {
            $this->node = $this->importNode(new DomElement('item'));
        }
        $this->_loadAttributes();
    }

    /**
     * @return void
     */
    private function _loadAttributes(): void
    {
        foreach ($this->node->attributes as $attributes) {
            if (array_key_exists($attributes->name, $this->possibilities)) {
                $this->{$attributes->name} = $attributes->value;
            }
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
        if (array_key_exists($name, $this->attr)) {
            return $this->attr[$name];
        }

        if (array_key_exists($name, $this->possibilities)) {
            $tmp = $this->node->getElementsByTagName($name);
            if ($tmp->length !== 1) {
                return null;
            }

            return $tmp->item(0)->nodeValue;
        }

        throw new SRSSException(sprintf('%s is not a possible item (%s)', $name, implode(', ', array_keys($this->possibilities))));
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
     * @throws SRSSException
     */
    public function __set($name, $val)
    {
        if (!array_key_exists($name, $this->possibilities)) {
            throw new SRSSException(sprintf('%s is not a possible item (%s)', $name, implode(', ', array_keys($this->possibilities))));
        }

        $flag = $this->possibilities[$name];

        if ($flag !== '') {
            $val = SRSSTools::check($val, $flag);
        }

        if (!empty($val)) {
            if ($this->$name === null) {
                $this->node->appendChild(new DomElement($name, $val));
            }
            $this->attr[$name] = $val;
        }
    }
}