<?php

namespace Shikiryu\SRSS\Entity;

class Item implements SRSSElement
{
    public ?string $title;
    public ?string $link;
    public ?string $description;
    public ?string $author;
    public ?string $category;
    public ?string $comments;
    public ?string $enclosure;
    public ?string $guid;
    public ?string $pubDate;
    public ?string $source;

    /**
     * @var \Shikiryu\SRSS\Entity\Media\Content[]
     */
    public array $medias = [];

    public array $required = ['description'];

    public function isValid(): bool
    {
        return count(array_filter($this->required, fn($field) => !empty($this->{$field}))) === 0;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}