<?php

namespace Shikiryu\SRSS\Entity\Channel;

class Image
{
    public string $url;
    public string $title;
    public string $link;
    public int $width; // Maximum value for width is 144, default value is 88.
    public int $height; //Maximum value for height is 400, default value is 31.
    public string $description;

    public array $required = ['url', 'title', 'link'];
}