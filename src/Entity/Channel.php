<?php

namespace Shikiryu\SRSS\Entity;

use Shikiryu\SRSS\Entity\Channel\Image;

class Channel
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
}