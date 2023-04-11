<?php

namespace Shikiryu\SRSS\Entity;

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
    /*
     * TODO can be multiple with attributes and all
     */
    public ?string $category = null;
    /**
     * @url
     */
    public ?string $comments = null;
    /*
     * TODO 1 attributes and 1 value
     */
    public ?string $enclosure = null;
    public ?string $guid = null;
    /**
     * @date
     */
    public ?string $pubDate = null;
    /*
     * TODO 1 attributes and 1 value
     */
    public ?string $source = null;

    /**
     * @var Content[]
     * @contentMedia
     */
    public array $medias = [];

    public function isValid(): bool
    {
        try {
            return (new Validator())->isObjectValid($this);
        } catch (\ReflectionException $e) {
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