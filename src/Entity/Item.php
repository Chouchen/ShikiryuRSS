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
 * @property null|string title
 * @property null|string link
 * @property null|string description
 * @property null|string author
 * @property null|array category
 * @property null|string comments
 * @property null|Enclosure enclosure
 * @property null|string guid
 * @property null|string pubDate
 * @property null|Source source
 * @property array medias
 */
class Item extends HasValidator implements SRSSElement
{
    /**
     * @validate requiredOr description
     * @format html
     */
    protected ?string $title = null;
    /**
     * @validate url
     * @format url
     */
    protected ?string $link = null;
    /**
     * @validate requiredOr title
     * @format html
     */
    protected ?string $description = null;
    /**
     * @validate email
     * @format email
     */
    protected ?string $author = null;
    /**
     * @var Category[]
     */
    protected ?array $category = null;
    /**
     * @validate url
     * @format url
     */
    protected ?string $comments = null;
    /**
     * @var Enclosure|null
     */
    protected ?Enclosure $enclosure = null;
    protected ?string $guid = null;
    /**
     * @validate date
     * @format date
     */
    protected ?string $pubDate = null;

    /**
     * @var Source|null
     */
    protected ?Source $source = null;

    /**
     * @var Content[]
     * @contentMedia
     */
    protected array $medias = [];

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