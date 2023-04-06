<?php

namespace Shikiryu\SRSS\Entity;

use Shikiryu\SRSS\Entity\Channel\Image;

class Channel implements SRSSElement
{
    public string $title;
    public string $link;
    public string $description;

    public ?string $language;
    public ?string $copyright;
    public ?string $managingEditor;
    public ?string $webMaster;
    public ?string $pubDate;
    public ?string $lastBuildDate;
    public ?string $category;
    public ?string $generator;
    public ?string $docs;
    public ?string $cloud;
    public ?string $ttl;
    public ?Image $image;
    public ?string $rating;
    public ?string $textInput;
    public ?string $skipHours;
    public ?string $skipDays;

    public array $required = ['title', 'link', 'description'];

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return count(array_filter($this->required, fn($field) => !empty($this->{$field}))) === 0;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}