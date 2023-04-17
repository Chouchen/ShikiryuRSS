<?php

namespace Shikiryu\SRSS\Entity\Channel;

use ReflectionException;
use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Image extends HasValidator implements SRSSElement
{
    /**
     * @validate required
     * @validate url
     * @format url
     */
    protected ?string $url = null;
    /**
     * @validate required
     * @validate nohtml
     * @format nohtml
     */
    protected ?string $title = null;
    /**
     * @validate required
     * @validate url
     * @format url
     */
    protected ?string $link = null;
    /**
     * @validate int
     * @format int
     * @validate max 144
     */
    protected int $width = 88; // Maximum value for width is 144, default value is 88.
    /**
     * @format int
     * @validate int
     * @validate max 400
     */
    protected int $height = 31; //Maximum value for height is 400, default value is 31.

    /**
     * @var string
     * @format html
     */
    protected string $description;

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
        return get_object_vars($this);
    }
}