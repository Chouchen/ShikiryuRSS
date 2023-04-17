<?php

namespace Shikiryu\SRSS\Entity;

use ReflectionException;
use Shikiryu\SRSS\Entity\Channel\Category;
use Shikiryu\SRSS\Entity\Channel\Cloud;
use Shikiryu\SRSS\Entity\Channel\Image;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

/**
 * https://cyber.harvard.edu/rss/rss.html#requiredChannelElements
 */
class Channel extends HasValidator implements SRSSElement
{
    /**
     * @validate required
     * @format html
     */
    protected string $title = '';

    /**
     * @validate required
     * @validate url
     * @format url
     */
    protected string $link = '';

    /**
     * @validate required
     * @format html
     */
    protected string $description = '';

    /**
     * @validate lang
     */
    protected ?string $language = null;
    /**
     * @validate nohtml
     * @format nohtml
     */
    protected ?string $copyright = null;
    /**
     * @validate nohtml
     * @format nohtml
     */
    protected ?string $managingEditor = null;
    /**
     * @validate nohtml
     * @format nohtml
     */
    protected ?string $webMaster = null;
    /**
     * @validate date
     * @format date
     */
    protected ?string $pubDate = null;
    /**
     * @validate date
     * @format date
     */
    protected ?string $lastBuildDate = null;
    /**
     * @var Category[]
     */
    protected ?array $category = null;
    /**
     * @validate nohtml
     * @format nohtml
     */
    protected ?string $generator = null;
    /**
     * @validate url
     * @format url
     */
    protected ?string $docs = null;
    /**
     * @var Cloud|null
     */
    protected ?Cloud $cloud = null;
    /**
     * @validate int
     * @format int
     */
    protected ?string $ttl = null;
    protected ?Image $image = null;
    protected ?string $rating = null;
    /**
     * @var string|null
     * The purpose of the <textInput> element is something of a mystery. You can use it to specify a search engine box. Or to allow a reader to provide feedback. Most aggregators ignore it.
     */
    protected ?string $textInput = null;
    /**
     * @validate hour
     * @format hour
     */
    protected ?string $skipHours = null;
    /**
     * @validate day
     * @format day
     */
    protected ?string $skipDays = null;

    /**
     * @return bool
     * @throws ReflectionException
     */
    public function isValid(): bool
    {
        return (new Validator())->isObjectValid($this);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $vars = get_object_vars($this);
        unset($vars['validated']);
        return $vars;
    }
}