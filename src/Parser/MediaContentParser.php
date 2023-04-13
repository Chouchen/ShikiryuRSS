<?php

namespace Shikiryu\SRSS\Parser;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use Shikiryu\SRSS\Entity\Item;
use Shikiryu\SRSS\Entity\Media\Content;
use Shikiryu\SRSS\Exception\SRSSException;
use Shikiryu\SRSS\SRSSTools;

/**
 * @property string|null $description
 */
class MediaContentParser extends DomDocument
{
    protected DOMNode $node; // item node

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
     * @param Content $item
     * @param $nodes
     *
     * @return void
     */
    private static function _loadChildAttributes(Content $item, $nodes): void
    {
        foreach ($nodes->attributes as $attribute) {

            if (property_exists(Content::class, $attribute->name)) {
                $item->{$attribute->name} = $attribute->value;
            }

        }
    }

    /**
     * @param DOMNode|null $node
     *
     * @return Content
     */
    public static function read(?DOMNode $node = null): Content
    {
        $content = new Content();
        if ($node instanceof DOMNode) {
            self::_loadChildAttributes($content, $node);
        }

        return $content;
    }
}