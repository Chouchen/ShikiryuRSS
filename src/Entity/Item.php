<?php

namespace Shikiryu\SRSS\Entity;

class Item
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

    public array $required = ['description'];
}