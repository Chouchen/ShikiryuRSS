<?php

namespace Shikiryu\SRSS\Entity\Channel;

use ReflectionException;
use Shikiryu\SRSS\Entity\SRSSElement;
use Shikiryu\SRSS\Validator\HasValidator;
use Shikiryu\SRSS\Validator\Validator;

class Image extends HasValidator implements SRSSElement
{
    /**
     * @required
     * @url
     */
    public ?string $url = null;
    /**
     * @required
     * @nohtml
     */
    public ?string $title = null;
    /**
     * @required
     * @url
     */
    public ?string $link = null;
    /**
     * @int
     * @max 144
     */
    public int $width = 88; // Maximum value for width is 144, default value is 88.
    /**
     * @int
     * @max 400
     */
    public int $height = 31; //Maximum value for height is 400, default value is 31.

    public string $description;

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