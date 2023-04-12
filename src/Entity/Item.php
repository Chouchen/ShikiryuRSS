<?php

namespace Shikiryu\SRSS\Entity;

use ReflectionException;
use Shikiryu\SRSS\Entity\Item\Category;
use Shikiryu\SRSS\Entity\Item\Enclosure;
use Shikiryu\SRSS\Entity\Item\Source;
use Shikiryu\SRSS\Entity\Media\Content;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

/**
 * https://cyber.harvard.edu/rss/rss.html#hrelementsOfLtitemgt
 */
class Item extends HasValidator implements SRSSElement
{
    /**
     * @requiredOr description
     * @nohtml
     */
    public ?string $title = null;
    /**
     * @url
     */
    public ?string $link = null;
    /**
     * @requiredOr title
     */
    public ?string $description = null;
    /**
     * @email
     */
    public ?string $author = null;
    /**
     * @var Category[]
     */
    public ?array $category = null;
    /**
     * @url
     */
    public ?string $comments = null;
    /**
     * @var Enclosure|null
     */
    public ?Enclosure $enclosure = null;
    public ?string $guid = null;
    /**
     * @date
     */
    public ?string $pubDate = null;

    /**
     * @var Source|null
     */
    public ?Source $source = null;

    /**
     * @var Content[]
     * @contentMedia
     */
    public array $medias = [];

    public function isValid(): bool
    {
        try {
            return (new Validator())->isObjectValid($this);
        } catch (ReflectionException) {
            return false;
        }
    }

    public function toArray(): array
    {
        $vars = get_object_vars($this);
        unset($vars['validated']);
        return $vars;
    }
}