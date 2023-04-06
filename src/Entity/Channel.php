<?php

namespace Shikiryu\SRSS\Entity;

use ReflectionException;
use Shikiryu\SRSS\Entity\Channel\Image;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

/**
 * https://cyber.harvard.edu/rss/rss.html#requiredChannelElements
 */
class Channel extends HasValidator implements SRSSElement
{
    /**
     * @required
     * @nohtml
     */
    public string $title;

    /**
     * @required
     * @url
     */
    public string $link;

    /**
     * @required
     */
    public string $description;

    /**
     * @lang
     */
    public ?string $language = null;
    /**
     * @nohtml
     */
    public ?string $copyright = null;
    /**
     * @nohtml
     */
    public ?string $managingEditor = null;
    /**
     * @nohtml
     */
    public ?string $webMaster = null;
    /**
     * @date
     */
    public ?string $pubDate = null;
    /**
     * @date
     */
    public ?string $lastBuildDate = null;
    /**
     * TODO should be an array
     */
    public ?string $category = null;
    /**
     * @nohtml
     */
    public ?string $generator = null;
    /**
     * @url
     */
    public ?string $docs = null;
    /**
     * @var string|null
     * TODO validator
     */
    public ?string $cloud = null;
    /**
     * @int
     */
    public ?string $ttl = null;
    public ?Image $image = null;
    public ?string $rating = null;
    /**
     * @var string|null
     * The purpose of the <textInput> element is something of a mystery. You can use it to specify a search engine box. Or to allow a reader to provide feedback. Most aggregators ignore it.
     */
    public ?string $textInput = null;
    /**
     * @hour
     */
    public ?string $skipHours = null;
    /**
     * @day
     */
    public ?string $skipDays = null;

    /**
     * @return bool
     * @throws ReflectionException
     */
    public function isValid(): bool
    {
        $annotation_validation = new Validator();

        return $annotation_validation->isObjectValid($this);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}